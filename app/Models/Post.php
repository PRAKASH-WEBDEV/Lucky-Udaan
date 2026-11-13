<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasSlug, HasStatus;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public const TYPES = [
        'blog' => 'Blog',
        'news' => 'News',
        'announcement' => 'Announcement',
        'press' => 'Press Release',
        'media' => 'Media Coverage',
    ];

    public function scopePublished($query)
    {
        return $query->active()->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at');
    }

    public function scopeOfType($query, string|array $type)
    {
        return $query->whereIn('type', (array) $type);
    }

    public function url(): string
    {
        return match ($this->type) {
            'announcement' => route('announcements.show', $this),
            'press' => route('press.show', $this),
            'media' => $this->external_url ?: route('blog.show', $this),
            default => route('blog.show', $this),
        };
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }
}
