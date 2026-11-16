<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
        ];
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
