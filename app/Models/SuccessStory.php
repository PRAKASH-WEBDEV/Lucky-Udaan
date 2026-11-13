<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
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

}
