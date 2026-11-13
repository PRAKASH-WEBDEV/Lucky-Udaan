<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'goal_amount' => 'decimal:2',
            'raised_amount' => 'decimal:2',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function progress(): int
    {
        return $this->goal_amount > 0 ? (int) min(100, round($this->raised_amount / $this->goal_amount * 100)) : 0;
    }
}
