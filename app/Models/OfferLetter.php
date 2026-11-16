<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferLetter extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'issued_on' => 'date',
        ];
    }

    public const STATUSES = ['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined'];
}
