<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'issued_on' => 'date',
            'valid_till' => 'date',
        ];
    }

}
