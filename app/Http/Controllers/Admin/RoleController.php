<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends ResourceController
{
    protected string $model = Role::class;

    protected string $route = 'admin.roles';

    protected string $title = 'Roles & Permissions';

    protected string $singular = 'Role';

    protected string $icon = 'key';

    protected array $searchable = ['name'];

    protected string $sort = 'id';

    protected string $direction = 'asc';

    protected function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Role::withCount('admins');
    }

    protected function fields(?Model $item): array
    {
        if ($item?->is_super) {
            return [
                ['name' => 'name', 'label' => 'Role name', 'rules' => 'required|string|max:60'],
                ['type' => 'static', 'label' => 'Permissions', 'value' => fn () => new \Illuminate\Support\HtmlString('<p class="mb-0">Super Admin always has full access to every module.</p>')],
            ];
        }

        return [
            ['name' => 'name', 'label' => 'Role name', 'rules' => 'required|string|max:60'],
            ['name' => 'permissions', 'label' => 'Module access', 'type' => 'checkboxes', 'options' => config('admin.modules'), 'columns' => 2,
                'help' => 'Admins with this role can only open the modules ticked here.'],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        if (! $item) {
            $base = Str::slug($data['name']);
            $slug = $base;
            $i = 2;
            while (Role::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }
            $data['slug'] = $slug;
        }

        return $data;
    }

    public function destroy(int $id)
    {
        $role = Role::withCount('admins')->findOrFail($id);
        if ($role->is_super || $role->admins_count > 0) {
            return back()->with('error', 'This role cannot be deleted while it is the Super Admin role or assigned to admin users.');
        }

        return parent::destroy($id);
    }

    protected function columns(): array
    {
        $modules = config('admin.modules');

        return [
            ['Role', fn ($r) => static::titleCell($r->name), 'name'],
            ['Access', fn ($r) => $r->is_super ? static::badge('All modules', 'primary')
                : (Str::limit(collect($r->permissions ?? [])->map(fn ($p) => Str::before($modules[$p] ?? $p, ' ('))->implode(', '), 90) ?: '—')],
            ['Users', fn ($r) => $r->admins_count, 'admins_count'],
        ];
    }
}
