<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_paid' => 'boolean',
            'carry_forward' => 'boolean',
        ];
    }

    public static function options(): array
    {
        return static::where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }
}
