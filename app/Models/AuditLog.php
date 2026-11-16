<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
