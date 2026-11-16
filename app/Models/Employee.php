<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'dob' => 'date',
            'joining_date' => 'date',
        ];
    }

    public const TYPES = ['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'intern' => 'Intern', 'volunteer' => 'Volunteer (stipend)'];

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /** Monthly gross: explicit salary, else the structure total. */
    public function monthlyGross(): float
    {
        return (float) ($this->monthly_salary ?: $this->salaryStructure?->gross() ?: 0);
    }

    public static function options(): array
    {
        return static::orderBy('name')->get()->mapWithKeys(fn ($e) => [$e->id => $e->name.($e->employee_code ? " ({$e->employee_code})" : '')])->all();
    }
}
