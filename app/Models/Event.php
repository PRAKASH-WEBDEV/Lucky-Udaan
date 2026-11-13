<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeUpcoming($query)
    {
        return $query->active()->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at');
    }

    public function scopePast($query)
    {
        return $query->active()->where('starts_at', '<', now()->startOfDay())->orderByDesc('starts_at');
    }

    public function isPast(): bool
    {
        return $this->starts_at->lt(now()->startOfDay());
    }
}
