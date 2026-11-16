<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /** Record a dashboard notification (never breaks the request that triggered it). */
    public static function notify(string $type, string $title, ?string $message = null, ?string $url = null): void
    {
        try {
            static::create(compact('type', 'title', 'message', 'url'));
        } catch (\Throwable) {
            // table missing during early migrations, etc.
        }
    }
}
