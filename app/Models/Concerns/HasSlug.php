<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates a unique slug from the title when none is supplied.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->slug ?: $model->{$model->slugSource ?? 'title'};
            $base = Str::slug(Str::limit((string) $source, 80, '')) ?: Str::random(8);
            if ($model->isDirty('slug') || ! $model->exists || ! $model->slug) {
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->when($model->exists, fn ($q) => $q->whereKeyNot($model->getKey()))->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
