<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
            'present_days' => 'float',
        ];
    }

    public const STATUSES = ['draft' => 'Draft', 'processed' => 'Processed', 'paid' => 'Paid'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function monthLabel(): string
    {
        return \Illuminate\Support\Carbon::createFromFormat('Y-m', $this->month)->format('F Y');
    }
}
