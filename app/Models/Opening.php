<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deadline' => 'date',
        ];
    }

    public function scopeOpen($query)
    {
        return $query->active()->where(fn ($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', today()));
    }
}
