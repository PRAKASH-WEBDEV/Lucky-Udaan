<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'date' => 'date',
        ];
    }

    public const TYPES = ['national' => 'National', 'festival' => 'Festival', 'optional' => 'Optional / restricted', 'organisation' => 'Organisation'];
}
