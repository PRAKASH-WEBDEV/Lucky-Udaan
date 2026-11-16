<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class HrDevice extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_sync_at' => 'datetime',
        ];
    }

    public const TYPES = ['biometric' => 'Fingerprint (biometric)', 'face' => 'Face recognition', 'rfid' => 'RFID / card', 'mobile' => 'Mobile app / GPS'];
}
