<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function embedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1];
        }

        return $this->video_url;
    }

    public function youtubeId(): ?string
    {
        return preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', (string) $this->video_url, $m) ? $m[1] : null;
    }
}
