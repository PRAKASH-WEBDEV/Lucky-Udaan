<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function highlightList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $this->highlights))));
    }
}
