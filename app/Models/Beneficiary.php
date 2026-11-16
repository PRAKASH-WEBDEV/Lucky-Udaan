<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'dob' => 'date',
            'enrolled_on' => 'date',
        ];
    }

    public const CATEGORIES = ['child' => 'Child', 'student' => 'Student', 'woman' => 'Woman', 'elderly' => 'Elderly', 'disabled' => 'Person with disability', 'family' => 'Family', 'patient' => 'Patient', 'other' => 'Other'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
