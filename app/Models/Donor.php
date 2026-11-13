<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
