<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Per-request SEO state. Controllers call seo()->set(...); the site layout renders it.
 */
class Seo
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $image = null;

    public ?string $canonical = null;

    public string $type = 'website';

    public bool $noindex = false;

    /** @var array<int, array<string, mixed>> extra JSON-LD blocks */
    public array $schemas = [];

    /** @var array<int, array{0:string,1:?string}> */
    public array $breadcrumbs = [];

    public function set(?string $title = null, ?string $description = null, ?string $image = null, string $type = 'website'): static
    {
        $this->title = $title;
        $this->description = $description ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($description))), 158) : null;
        $this->image = $image;
        $this->type = $type;

        return $this;
    }

    /** Use a CMS page's meta fields (falling back to its title/subtitle). */
    public function fromPage($page, ?string $fallbackTitle = null): static
    {
        return $this->set(
            $page?->meta_title ?: ($page?->title ?? $fallbackTitle),
            $page?->meta_description ?: $page?->subtitle,
            $page?->image ? upload_url($page->image) : null,
        );
    }

    public function breadcrumbs(array $crumbs): static
    {
        $this->breadcrumbs = $crumbs;

        return $this;
    }

    public function schema(array $schema): static
    {
        $this->schemas[] = $schema;

        return $this;
    }

    public function fullTitle(): string
    {
        $site = setting('site_name', config('app.name'));
        if (! $this->title) {
            return setting('seo_title', $site.' | NGO in Gonda, Uttar Pradesh');
        }

        return Str::contains($this->title, $site) ? $this->title : $this->title.' | '.$site;
    }

    public function metaDescription(): string
    {
        return $this->description ?: (string) setting('seo_description', '');
    }

    public function ogImage(): ?string
    {
        return $this->image ?: upload_url(setting('og_image')) ?: upload_url(setting('logo'));
    }
}
