<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->role?->is_super;
    }

    /** Whether this admin may access the given dashboard module key. */
    public function canAccess(string $module): bool
    {
        if (! $this->is_active || ! $this->role) {
            return false;
        }

        return $this->role->is_super || in_array($module, $this->role->permissions ?? [], true);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminResetPassword($token));
    }
}
