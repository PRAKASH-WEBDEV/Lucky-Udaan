<?php

namespace App\Http\Controllers\Admin;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GalleryController extends ResourceController
{
    protected string $model = GalleryItem::class;

    protected string $route = 'admin.gallery';

    protected string $title = 'Gallery';

    protected string $singular = 'Gallery item';

    protected string $icon = 'camera';

    protected string $uploadDir = 'gallery';

    protected array $searchable = ['title', 'category'];

    protected function categories(): array
    {
        $existing = GalleryItem::whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all();

        return array_values(array_unique(array_merge(['Education', 'Health', 'Women Empowerment', 'Environment', 'Relief', 'Events'], $existing)));
    }

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Caption', 'rules' => 'required|string|max:200', 'help' => 'Also used as the image alt text for accessibility and SEO.'],
            ['name' => 'category', 'label' => 'Album / category', 'col' => 6, 'rules' => 'nullable|string|max:60', 'datalist' => $this->categories()],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 6, 'options' => ['photo' => 'Photo', 'video' => 'Video (YouTube)'], 'rules' => 'required|in:photo,video'],
            ['name' => 'video_url', 'label' => 'YouTube link', 'type' => 'url', 'rules' => 'nullable|url|max:255', 'help' => 'Only for videos. The image becomes the thumbnail (optional for YouTube).'],
            $this->imageField('image', 'Photo'),
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return [
            'type' => ['Type', ['photo' => 'Photos', 'video' => 'Videos']],
            'category' => ['Album', array_combine($this->categories(), $this->categories())],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        if ($data['type'] === 'photo' && empty($data['image']) && ! $item?->image) {
            throw ValidationException::withMessages(['image' => 'Please upload a photo.']);
        }
        if ($data['type'] === 'video' && empty($data['video_url'])) {
            throw ValidationException::withMessages(['video_url' => 'Please add the YouTube link for this video.']);
        }

        return $data;
    }

    protected function columns(): array
    {
        return [
            ['', fn ($g) => $g->image ? static::thumb($g->image, $g->title)
                : new \Illuminate\Support\HtmlString('<img class="table-thumb" src="https://i.ytimg.com/vi/'.e($g->youtubeId()).'/mqdefault.jpg" alt="" loading="lazy">')],
            ['Caption', fn ($g) => static::titleCell($g->title, $g->category), 'title'],
            ['Type', fn ($g) => static::badge(ucfirst($g->type), $g->type === 'video' ? 'danger' : 'info'), 'type'],
            ['Order', fn ($g) => $g->sort_order, 'sort_order'],
        ];
    }
}
