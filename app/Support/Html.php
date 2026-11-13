<?php

namespace App\Support;

/**
 * Light sanitiser for rich-text written in the dashboard editor.
 * Keeps formatting tags, strips scripts, event handlers and javascript: URLs.
 */
class Html
{
    private const ALLOWED = '<p><br><strong><b><em><i><u><s><a><ul><ol><li><h2><h3><h4><h5><blockquote><pre><code><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><span><iframe>';

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }
        $html = preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, self::ALLOWED);
        // Remove inline event handlers and style attributes.
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);
        // Neutralise javascript:/data: URLs in href/src.
        $html = preg_replace('/\s(href|src)\s*=\s*(["\']?)\s*(javascript|vbscript|data):[^"\'>\s]*\2/i', ' $1="#"', $html);
        // Only allow YouTube / Google Maps iframes.
        $html = preg_replace_callback('#<iframe[^>]*src=["\']([^"\']+)["\'][^>]*>.*?</iframe>#is', function ($m) {
            return preg_match('#^https://(www\.)?(youtube(-nocookie)?\.com|google\.com/maps)/#', $m[1])
                ? '<div class="ratio ratio-16x9"><iframe src="'.e($m[1]).'" loading="lazy" allowfullscreen title="Embedded media"></iframe></div>'
                : '';
        }, $html);

        return $html;
    }
}
