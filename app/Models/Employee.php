<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;

/** Staff record; also the sign-in account for the staff portal (guard "employee"). */
class Employee extends Authenticatable
{
    use HasStatus;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'portal_access' => 'boolean',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
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

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
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
