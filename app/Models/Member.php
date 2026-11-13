<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'dob' => 'date',
            'valid_till' => 'date',
        ];
    }

    public const TYPES = ['general' => 'General Member', 'life' => 'Life Member', 'student' => 'Student Member', 'patron' => 'Patron'];
}
