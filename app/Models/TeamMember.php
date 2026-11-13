<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public const GROUPS = ['board' => 'Board of Directors', 'team' => 'Core Team', 'advisor' => 'Advisors'];

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    }
}
