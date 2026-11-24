<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Staff portal: lets an employee sign in with email + password to mark their own attendance. */
return new class extends Migration
{
    /** Run outside a transaction and skip existing columns, so a half-finished run can simply be retried. */
    public $withinTransaction = false;

    public function up(): void
    {
        $columns = [
            'password' => fn (Blueprint $t) => $t->string('password')->nullable(),
            'remember_token' => fn (Blueprint $t) => $t->rememberToken(),
            'portal_access' => fn (Blueprint $t) => $t->boolean('portal_access')->default(false),
            'last_login_at' => fn (Blueprint $t) => $t->timestamp('last_login_at')->nullable(),
        ];

        foreach ($columns as $name => $add) {
            if (! Schema::hasColumn('employees', $name)) {
                Schema::table('employees', $add);
            }
        }
    }

    public function down(): void
    {
        foreach (['password', 'remember_token', 'portal_access', 'last_login_at'] as $name) {
            if (Schema::hasColumn('employees', $name)) {
                Schema::table('employees', fn (Blueprint $t) => $t->dropColumn($name));
            }
        }
    }
};
