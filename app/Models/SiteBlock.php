<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class SiteBlock extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /** Active blocks of one type, in display order. */
    public static function list(string $type)
    {
        return static::ofType($type)->active()->ordered()->get();
    }
}
