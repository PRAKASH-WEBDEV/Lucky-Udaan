<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public const STATUSES = ['present' => 'Present', 'late' => 'Late', 'half_day' => 'Half day', 'absent' => 'Absent', 'leave' => 'On leave', 'holiday' => 'Holiday'];

    public const SOURCES = ['manual' => 'Manual punch', 'device' => 'Device', 'web' => 'Web'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
