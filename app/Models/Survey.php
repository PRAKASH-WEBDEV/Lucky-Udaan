<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'ends_on' => 'date',
        ];
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /** Questions, one per line. */
    public function questionList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->questions))));
    }

    public function isOpen(): bool
    {
        return $this->is_active && (! $this->ends_on || $this->ends_on->gte(today()));
    }
}
