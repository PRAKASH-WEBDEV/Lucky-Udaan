<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'interests' => 'array',
        ];
    }

}
