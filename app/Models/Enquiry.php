<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public const TYPES = ['contact' => 'Contact', 'partnership' => 'Partnership', 'career' => 'Career', 'internship' => 'Internship'];

    public function opening()
    {
        return $this->belongsTo(Opening::class);
    }
}
