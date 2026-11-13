<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Html;
use App\Support\Uploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared list / create / edit / delete / toggle / export behaviour for dashboard modules.
 * Each module controller describes its fields, list columns and filters; the generic
 * Blade views in resources/views/admin/resource render them consistently.
 */
abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** Route name prefix, e.g. "admin.programs". */
    protected string $route;

    protected string $title;

    protected string $singular;

    protected string $icon = 'collection';

    protected string $uploadDir = 'misc';

    /** Columns searched by the list's search box. */
    protected array $searchable = ['title'];

    protected array $with = [];

    protected string $sort = 'id';

    protected string $direction = 'desc';

    /** Column used by the date-range filter (null hides it). */
    protected ?string $dateColumn = null;

    protected bool $canCreate = true;

    protected bool $canDelete = true;

    protected int $perPage = 15;

    /** Form field definitions. */
    abstract protected function fields(?Model $item): array;

    /** List columns: [label, render(Model): string|HtmlString, sortColumn?]. */
    abstract protected function columns(): array;

    /** Extra list filters: name => [label, options, column?]. */
    protected function filters(): array
    {
        return [];
    }

    /** Columns for CSV export: label => closure|attribute. Empty disables export. */
    protected function exportColumns(): array
    {
        return [];
    }

    /** Hook: adjust validated data before saving. */
    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        return $data;
    }

    /** Hook: runs after create/update. */
    protected function afterSave(Model $item, Request $request, bool $created): void {}

    /** Extra per-row action links: [[label, url, icon], ...]. */
    protected function rowActions(Model $item): array
    {
        return [];
    }

    protected function newQuery(): Builder
    {
        return $this->model::query()->with($this->with);
    }

    // ---------------------------------------------------------------- actions

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $sort = $request->string('sort')->toString();
        $sortable = collect($this->columns())->pluck(2)->filter()->all();
        if ($sort && in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $request->get('dir') === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy($this->sort, $this->direction);
        }

        $items = $query->paginate($this->perPage)->withQueryString();

        return view('admin.resource.index', $this->viewData([
            'items' => $items,
            'columns' => $this->columns(),
            'filters' => $this->filters(),
            'hasStatus' => $this->hasColumn('is_active'),
            'exportable' => ! empty($this->exportColumns()),
            'controller' => $this,
        ]));
    }

    public function create()
    {
        abort_unless($this->canCreate, 404);

        return view('admin.resource.form', $this->viewData([
            'item' => new $this->model,
            'fields' => $this->fields(null),
        ]));
    }

    public function store(Request $request)
    {
        abort_unless($this->canCreate, 404);
        $item = new $this->model;
        $item->fill($this->validatedData($request, null))->save();
        $this->afterSave($item, $request, true);

        return $this->redirectAfterSave($request, $item, "{$this->singular} created successfully.");
    }

    public function show(int $id)
    {
        return redirect()->route($this->route.'.edit', $id);
    }

    public function edit(int $id)
    {
        $item = $this->newQuery()->findOrFail($id);

        return view('admin.resource.form', $this->viewData([
            'item' => $item,
            'fields' => $this->fields($item),
            'actions' => $this->rowActions($item),
        ]));
    }

    public function update(Request $request, int $id)
    {
        $item = $this->model::findOrFail($id);
        $item->fill($this->validatedData($request, $item))->save();
        $this->afterSave($item, $request, false);

        return $this->redirectAfterSave($request, $item, "{$this->singular} updated successfully.");
    }

    public function destroy(int $id)
    {
        abort_unless($this->canDelete, 404);
        $item = $this->model::findOrFail($id);
        $this->beforeDelete($item);
        $item->delete();

        return redirect()->route($this->route.'.index')->with('success', "{$this->singular} deleted.");
    }

    public function toggle(int $id)
    {
        $item = $this->model::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return back()->with('success', "{$this->singular} marked ".($item->is_active ? 'active' : 'inactive').'.');
    }

    public function export(Request $request): StreamedResponse
    {
        $columns = $this->exportColumns();
        abort_if(empty($columns), 404);
        $query = $this->filteredQuery($request)->orderBy($this->sort, $this->direction);
        $filename = Str::slug($this->title).'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows ₹ and Hindi text correctly
            fputcsv($out, array_keys($columns));
            $query->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    fputcsv($out, array_map(function ($col) use ($row) {
                        $v = is_callable($col) ? $col($row) : data_get($row, $col);

                        return is_array($v) ? implode(', ', $v) : (string) $v;
                    }, $columns));
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ---------------------------------------------------------------- helpers

    protected function filteredQuery(Request $request): Builder
    {
        $query = $this->newQuery();

        if ($q = trim((string) $request->get('q'))) {
            $query->where(function (Builder $w) use ($q) {
                foreach ($this->searchable as $col) {
                    $w->orWhere($col, 'like', '%'.$q.'%');
                }
            });
        }

        if ($this->hasColumn('is_active') && in_array($request->get('active'), ['1', '0'], true)) {
            $query->where('is_active', $request->get('active') === '1');
        }

        foreach ($this->filters() as $name => $filter) {
            $value = $request->get($name);
            if ($value !== null && $value !== '' && array_key_exists($value, $filter[1])) {
                $query->where($filter[2] ?? $name, $value);
            }
        }

        if ($this->dateColumn) {
            if ($from = $request->date('from')) {
                $query->where($this->dateColumn, '>=', $from->startOfDay());
            }
            if ($to = $request->date('to')) {
                $query->where($this->dateColumn, '<=', $to->endOfDay());
            }
        }

        return $query;
    }

    protected function validatedData(Request $request, ?Model $item): array
    {
        $fields = collect($this->fields($item))->filter(fn ($f) => isset($f['name']) && ! in_array($f['type'] ?? 'text', ['static', 'section'], true));

        $rules = [];
        $attributes = [];
        foreach ($fields as $f) {
            $type = $f['type'] ?? 'text';
            $rule = $f['rules'] ?? ['nullable'];
            $rule = is_string($rule) ? explode('|', $rule) : $rule;
            $rule = match ($type) {
                'image' => array_merge($rule, ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.($f['max'] ?? 5120), 'dimensions:max_width=8000,max_height=8000']),
                'file' => array_merge($rule, ['file', 'mimes:'.($f['mimes'] ?? 'pdf'), 'max:'.($f['max'] ?? 10240)]),
                'switch' => ['nullable'],
                'checkboxes' => array_merge($rule, ['array']),
                default => $rule,
            };
            $rules[$f['name']] = $rule;
            if ($type === 'checkboxes') {
                $rules[$f['name'].'.*'] = ['string', 'in:'.implode(',', array_keys($f['options']))];
            }
            $attributes[$f['name']] = strtolower($f['label'] ?? $f['name']);
        }

        $validated = $request->validate($rules, [], $attributes);

        $data = [];
        foreach ($fields as $f) {
            $name = $f['name'];
            $type = $f['type'] ?? 'text';
            switch ($type) {
                case 'switch':
                    $data[$name] = $request->boolean($name);
                    break;
                case 'editor':
                    $data[$name] = Html::clean($validated[$name] ?? null);
                    break;
                case 'checkboxes':
                    $data[$name] = array_values($validated[$name] ?? []);
                    break;
                case 'image':
                case 'file':
                    if ($request->hasFile($name)) {
                        Uploader::delete($item?->{$name});
                        $data[$name] = Uploader::store($request->file($name), $f['dir'] ?? $this->uploadDir);
                    } elseif ($request->boolean('remove_'.$name) && empty($f['required'])) {
                        Uploader::delete($item?->{$name});
                        $data[$name] = null;
                    }
                    break;
                case 'password':
                    if (! empty($validated[$name])) {
                        $data[$name] = $validated[$name];
                    }
                    break;
                default:
                    if (array_key_exists($name, $validated)) {
                        $data[$name] = $validated[$name] ?? ($f['default'] ?? null);
                    }
            }
        }

        return $this->prepare($data, $item, $request);
    }

    protected function beforeDelete(Model $item): void
    {
        foreach ($this->fields($item) as $f) {
            if (in_array($f['type'] ?? '', ['image', 'file'], true)) {
                Uploader::delete($item->{$f['name']});
            }
        }
    }

    protected function redirectAfterSave(Request $request, Model $item, string $message)
    {
        if ($request->input('_after') === 'new') {
            return redirect()->route($this->route.'.create')->with('success', $message);
        }
        if ($request->input('_after') === 'stay') {
            return redirect()->route($this->route.'.edit', $item->getKey())->with('success', $message);
        }

        return redirect()->route($this->route.'.index')->with('success', $message);
    }

    protected function hasColumn(string $column): bool
    {
        static $cache = [];
        $table = (new $this->model)->getTable();

        return $cache[$table.$column] ??= \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    }

    protected function viewData(array $extra = []): array
    {
        return array_merge([
            'title' => $this->title,
            'singular' => $this->singular,
            'route' => $this->route,
            'icon' => $this->icon,
            'canCreate' => $this->canCreate,
            'canDelete' => $this->canDelete,
            'dateFilter' => (bool) $this->dateColumn,
            'searchPlaceholder' => 'Search '.Str::lower($this->title).'…',
        ], $extra);
    }

    public function actionsFor(Model $item): array
    {
        return $this->rowActions($item);
    }

    // ------------------------------------------------- column render helpers

    protected static function thumb(?string $path, string $alt = ''): HtmlString
    {
        $url = upload_small_url($path) ?: upload_url($path);

        return new HtmlString($url
            ? '<img src="'.e($url).'" alt="'.e($alt).'" class="table-thumb" loading="lazy">'
            : '<span class="table-thumb table-thumb--empty"><i class="bi bi-image"></i></span>');
    }

    protected static function titleCell(string $title, ?string $sub = null): HtmlString
    {
        return new HtmlString('<div class="fw-semibold text-dark">'.e($title).'</div>'
            .($sub ? '<div class="small text-muted text-truncate-2">'.e(Str::limit(strip_tags($sub), 90)).'</div>' : ''));
    }

    protected static function badge(?string $text, string $tone = 'secondary'): HtmlString
    {
        return new HtmlString($text ? '<span class="badge-soft badge-soft--'.$tone.'">'.e($text).'</span>' : '<span class="text-muted">—</span>');
    }

    protected static function statusTone(?string $status): string
    {
        return match ($status) {
            'confirmed', 'approved', 'replied', 'active' => 'success',
            'pending', 'new' => 'warning',
            'failed', 'rejected', 'closed' => 'danger',
            'read' => 'info',
            default => 'secondary',
        };
    }

    // ------------------------------------------------- common field presets

    protected function slugField(?Model $item, string $table): array
    {
        return ['name' => 'slug', 'label' => 'URL slug', 'type' => 'text', 'col' => 12,
            'rules' => ['nullable', 'alpha_dash', 'max:180', \Illuminate\Validation\Rule::unique($table, 'slug')->ignore($item?->getKey())],
            'help' => 'Leave blank to generate automatically from the title.'];
    }

    protected function seoFields(): array
    {
        return [
            ['type' => 'section', 'label' => 'Search engine (SEO)', 'help' => 'Optional. Falls back to the title and summary.'],
            ['name' => 'meta_title', 'label' => 'Meta title', 'rules' => 'nullable|string|max:70', 'col' => 12, 'counter' => 60],
            ['name' => 'meta_description', 'label' => 'Meta description', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:300', 'counter' => 158],
        ];
    }

    protected function activeField(): array
    {
        return ['name' => 'is_active', 'label' => 'Active (visible on website)', 'type' => 'switch', 'side' => true, 'default' => true];
    }

    protected function featuredField(string $label = 'Featured on homepage'): array
    {
        return ['name' => 'is_featured', 'label' => $label, 'type' => 'switch', 'side' => true];
    }

    protected function sortField(): array
    {
        return ['name' => 'sort_order', 'label' => 'Display order', 'type' => 'number', 'side' => true, 'rules' => 'nullable|integer|min:0|max:9999', 'default' => 0, 'help' => 'Lower numbers appear first.'];
    }

    protected function imageField(string $name = 'image', string $label = 'Image', string $help = 'JPG, PNG or WebP up to 5 MB. Optimised automatically.'): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'image', 'side' => true, 'help' => $help];
    }

    /** Options for program icons. */
    protected static function iconOptions(): array
    {
        return collect(config('admin.icons'))->mapWithKeys(fn ($i) => [$i => $i])->all();
    }
}
