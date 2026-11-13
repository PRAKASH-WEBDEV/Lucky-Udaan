<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends ResourceController
{
    protected string $model = Admin::class;

    protected string $route = 'admin.admins';

    protected string $title = 'Admin Users';

    protected string $singular = 'Admin user';

    protected string $icon = 'shield-lock';

    protected string $uploadDir = 'admins';

    protected array $searchable = ['name', 'email'];

    protected array $with = ['role'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'email', 'label' => 'Email (login)', 'type' => 'email', 'col' => 6, 'rules' => ['required', 'email', 'max:150', Rule::unique('admins', 'email')->ignore($item?->id)]],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 6, 'rules' => 'nullable|string|max:20'],
            ['name' => 'role_id', 'label' => 'Role', 'type' => 'select', 'col' => 6, 'options' => Role::orderBy('id')->pluck('name', 'id')->all(), 'rules' => 'required|exists:roles,id'],
            ['type' => 'section', 'label' => $item?->exists ? 'Change password' : 'Password', 'help' => $item?->exists ? 'Leave blank to keep the current password.' : 'At least 8 characters with letters and numbers.'],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'col' => 6,
                'rules' => [$item?->exists ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()]],
            ['name' => 'password_confirmation', 'label' => 'Confirm password', 'type' => 'password', 'col' => 6, 'rules' => ['nullable'], 'virtual' => true],
            $this->imageField('avatar', 'Profile photo'),
            ['name' => 'is_active', 'label' => 'Account active', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        unset($data['password_confirmation']);
        if ($item && $item->is($request->user('admin'))) {
            $data['is_active'] = true; // cannot lock yourself out
            if (! $item->isSuperAdmin() || (int) $data['role_id'] !== $item->role_id) {
                $data['role_id'] = $item->role_id; // cannot change your own role
            }
        }

        return $data;
    }

    public function destroy(int $id)
    {
        abort_if($id === auth('admin')->id(), 403, 'You cannot delete your own account.');
        $target = Admin::findOrFail($id);
        if ($target->isSuperAdmin() && Admin::whereHas('role', fn ($q) => $q->where('is_super', true))->count() <= 1) {
            return back()->with('error', 'At least one Super Admin must remain.');
        }

        return parent::destroy($id);
    }

    public function toggle(int $id)
    {
        if ($id === auth('admin')->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        return parent::toggle($id);
    }

    protected function columns(): array
    {
        return [
            ['', fn ($a) => static::thumb($a->avatar, $a->name)],
            ['Name', fn ($a) => static::titleCell($a->name, $a->email), 'name'],
            ['Role', fn ($a) => static::badge($a->role?->name ?? 'No role', $a->role?->is_super ? 'primary' : 'info')],
            ['Last login', fn ($a) => $a->last_login_at?->diffForHumans() ?? 'Never', 'last_login_at'],
        ];
    }
}
