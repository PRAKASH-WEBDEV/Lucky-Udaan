<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-off data migration: copies every application table from the local MySQL
 * database (connection "mysql_source") into Postgres (connection "pgsql").
 *
 * The Postgres schema must already exist (run `php artisan migrate` against it
 * first). Tables are copied parent-first based on the real foreign keys in
 * Postgres, then every id sequence is reset so new inserts don't collide.
 */
class CopyMysqlToPgsql extends Command
{
    protected $signature = 'db:copy-mysql-to-pgsql
        {--source=mysql_source : Source (MySQL) connection name}
        {--target=pgsql : Target (Postgres) connection name}
        {--chunk=500 : Rows per insert batch}
        {--truncate : Empty the target tables first (RESTART IDENTITY CASCADE)}
        {--with-framework : Also copy sessions, cache, cache_locks, jobs, job_batches, failed_jobs}
        {--force : Do not ask for confirmation}';

    protected $description = 'Copy all application data from the local MySQL database into Postgres (Neon)';

    /** Never copied: rebuilt by `migrate` itself. */
    private const ALWAYS_SKIP = ['migrations'];

    /** Runtime-only framework tables; skipped unless --with-framework. */
    private const FRAMEWORK_TABLES = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    public function handle(): int
    {
        /** @var Connection $src */
        $src = DB::connection($this->option('source'));
        /** @var Connection $dst */
        $dst = DB::connection($this->option('target'));

        if ($dst->getDriverName() !== 'pgsql') {
            $this->error("Target connection [{$dst->getName()}] is not pgsql.");

            return self::FAILURE;
        }
        if (! in_array($src->getDriverName(), ['mysql', 'mariadb'], true)) {
            $this->error("Source connection [{$src->getName()}] is not mysql.");

            return self::FAILURE;
        }

        $srcTables = $this->tableNames($src);
        $dstTables = $this->tableNames($dst);

        if ($dstTables === []) {
            $this->error('Target database has no tables. Run `php artisan migrate --force` against it first.');

            return self::FAILURE;
        }

        $skip = self::ALWAYS_SKIP;
        if (! $this->option('with-framework')) {
            $skip = array_merge($skip, self::FRAMEWORK_TABLES);
        }

        $tables = array_values(array_diff(array_intersect($dstTables, $srcTables), $skip));
        $onlyInSource = array_diff($srcTables, $dstTables, $skip);
        if ($onlyInSource) {
            $this->warn('Skipping tables that exist only in MySQL (no migration for them): '.implode(', ', $onlyInSource));
        }

        $tables = $this->sortByForeignKeys($dst, $tables);

        $this->info('Source: '.$src->getDatabaseName().' ('.$src->getDriverName().')');
        $this->info('Target: '.$dst->getDatabaseName().' on '.$dst->getConfig('host').' (pgsql)');
        $this->line('Copy order: '.implode(' -> ', $tables));

        // Refuse to append on top of existing data unless explicitly asked to wipe it.
        $nonEmpty = array_filter($tables, fn ($t) => $dst->table($t)->exists());
        if ($nonEmpty && ! $this->option('truncate')) {
            $this->error('Target tables already contain data: '.implode(', ', $nonEmpty));
            $this->line('Re-run with --truncate to empty them first.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Copy '.count($tables).' tables into the target database?', true)) {
            return self::FAILURE;
        }

        if ($this->option('truncate') && $tables) {
            $list = implode(', ', array_map(fn ($t) => $dst->getQueryGrammar()->wrapTable($t), $tables));
            $dst->statement("TRUNCATE TABLE {$list} RESTART IDENTITY CASCADE");
            $this->line('Truncated target tables.');
        }

        $chunk = max(1, (int) $this->option('chunk'));
        $failed = false;

        foreach ($tables as $table) {
            try {
                $copied = $this->copyTable($src, $dst, $table, $chunk);
                $sourceCount = $src->table($table)->count();
                $status = $copied === $sourceCount ? '<info>ok</info>' : '<error>MISMATCH</error>';
                $this->line(sprintf('  %-32s %6d / %6d  %s', $table, $copied, $sourceCount, $status));
                $failed = $failed || $copied !== $sourceCount;
            } catch (\Throwable $e) {
                $this->error("  {$table}: ".$e->getMessage());

                return self::FAILURE;
            }
        }

        $this->resetSequences($dst, $tables);

        if ($failed) {
            $this->error('Finished with row-count mismatches (see above).');

            return self::FAILURE;
        }

        $this->info('Done. All rows copied and sequences reset.');

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function tableNames(Connection $conn): array
    {
        $schema = $conn->getDriverName() === 'pgsql' ? 'public' : $conn->getDatabaseName();

        return collect(Schema::connection($conn->getName())->getTables())
            ->filter(fn ($t) => ($t['schema'] ?? $schema) === $schema)
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * Topological sort so referenced (parent) tables are filled before children.
     *
     * @param  list<string>  $tables
     * @return list<string>
     */
    private function sortByForeignKeys(Connection $dst, array $tables): array
    {
        $schema = Schema::connection($dst->getName());
        $deps = [];
        foreach ($tables as $t) {
            $deps[$t] = collect($schema->getForeignKeys($t))
                ->pluck('foreign_table')
                ->filter(fn ($p) => $p !== $t && in_array($p, $tables, true))
                ->unique()->values()->all();
        }

        $sorted = [];
        $visiting = [];
        $visit = function (string $t) use (&$visit, &$sorted, &$visiting, $deps) {
            if (in_array($t, $sorted, true)) {
                return;
            }
            if (isset($visiting[$t])) {
                throw new \RuntimeException("Circular foreign key involving [{$t}].");
            }
            $visiting[$t] = true;
            foreach ($deps[$t] as $parent) {
                $visit($parent);
            }
            unset($visiting[$t]);
            $sorted[] = $t;
        };

        sort($tables);
        foreach ($tables as $t) {
            $visit($t);
        }

        return $sorted;
    }

    private function copyTable(Connection $src, Connection $dst, string $table, int $chunk): int
    {
        $dstColumns = collect(Schema::connection($dst->getName())->getColumns($table))->keyBy('name');
        $srcColumns = Schema::connection($src->getName())->getColumnListing($table);
        $columns = array_values(array_intersect($srcColumns, $dstColumns->keys()->all()));

        $types = $dstColumns->map(fn ($c) => strtolower($c['type_name']))->all();
        $nullable = $dstColumns->map(fn ($c) => (bool) $c['nullable'])->all();

        $orderBy = in_array('id', $columns, true) ? 'id' : $columns[0];
        $copied = 0;

        $dst->transaction(function () use ($src, $dst, $table, $columns, $types, $nullable, $orderBy, $chunk, &$copied) {
            $src->table($table)->select($columns)->orderBy($orderBy)
                ->chunk($chunk, function ($rows) use ($dst, $table, $types, $nullable, &$copied) {
                    $batch = [];
                    foreach ($rows as $row) {
                        $out = [];
                        foreach ((array) $row as $col => $value) {
                            $out[$col] = $this->convert($value, $types[$col], $nullable[$col]);
                        }
                        $batch[] = $out;
                    }
                    $dst->table($table)->insert($batch);
                    $copied += count($batch);
                });
        });

        return $copied;
    }

    /** Adapt a MySQL value to what Postgres expects for the target column type. */
    private function convert(mixed $value, string $type, bool $nullable): mixed
    {
        if ($value === null) {
            return null;
        }

        // MySQL booleans are tinyint(1) 0/1 (sometimes returned as strings).
        if ($type === 'bool' || $type === 'boolean') {
            return in_array($value, [1, '1', true, 't', 'true'], true);
        }

        // MySQL "zero dates" are invalid in Postgres.
        if (in_array($type, ['date', 'timestamp', 'timestamptz', 'timestamp without time zone', 'timestamp with time zone'], true)
            && is_string($value) && str_starts_with($value, '0000-00-00')) {
            return $nullable ? null : '1970-01-01 00:00:00';
        }

        // Postgres text cannot contain NUL bytes.
        if (is_string($value) && ! in_array($type, ['bytea'], true) && str_contains($value, "\0")) {
            return str_replace("\0", '', $value);
        }

        return $value;
    }

    /** @param  list<string>  $tables */
    private function resetSequences(Connection $dst, array $tables): void
    {
        foreach ($tables as $table) {
            if (! Schema::connection($dst->getName())->hasColumn($table, 'id')) {
                continue;
            }
            // Third arg: when the table is empty the next id is 1, otherwise MAX(id)+1.
            $dst->select(
                "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE(MAX(id), 1), MAX(id) IS NOT NULL) FROM \"{$table}\"",
                [$table]
            );
        }
        $this->line('Sequences reset.');
    }
}
