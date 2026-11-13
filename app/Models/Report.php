<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public const TYPES = ['annual' => 'Annual Report', 'financial' => 'Financial Statement', 'audit' => 'Audit Report', 'impact' => 'Impact Report'];
}
