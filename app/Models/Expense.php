<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
