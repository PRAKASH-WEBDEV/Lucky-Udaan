<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_super' => 'boolean',
        ];
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
