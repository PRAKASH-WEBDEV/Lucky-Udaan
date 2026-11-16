<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'renewal_date' => 'date',
        ];
    }

    public const CYCLES = ['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly', 'one_time' => 'One time'];

    public const STATUSES = ['active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'];
}
