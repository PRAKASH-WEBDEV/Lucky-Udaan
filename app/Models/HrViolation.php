<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrViolation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public const TYPES = ['late_arrival' => 'Late arrival', 'early_exit' => 'Early exit', 'absent_without_leave' => 'Absent without leave', 'missed_punch' => 'Missed punch', 'misconduct' => 'Misconduct', 'other' => 'Other'];

    public const STATUSES = ['open' => 'Open', 'warned' => 'Warning issued', 'resolved' => 'Resolved'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
