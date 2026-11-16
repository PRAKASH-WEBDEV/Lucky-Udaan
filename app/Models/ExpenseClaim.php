<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseClaim extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'claim_date' => 'date',
            'paid_on' => 'date',
        ];
    }

    public const STATUSES = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'paid' => 'Paid'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
