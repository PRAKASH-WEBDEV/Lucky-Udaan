<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use App\Models\Program;
use App\Models\SuccessStory;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $urls = [];
            $add = function (string $loc, $lastmod = null, string $freq = 'monthly', string $priority = '0.6') use (&$urls) {
                $urls[] = compact('loc', 'freq', 'priority') + ['lastmod' => $lastmod?->toAtomString()];
            };

            $add(route('home'), now(), 'daily', '1.0');
            $static = ['about', 'programs.index', 'campaigns.index', 'events.index', 'blog.index', 'announcements.index', 'press.index',
                'transparency', 'contact', 'volunteer', 'membership', 'careers', 'gallery', 'team', 'stories.index', 'faq',
                'certifications', 'media', 'partnership', 'internships', 'reports', 'testimonials', 'donate', 'privacy', 'terms', 'refund'];
            $inactive = Page::where('is_active', false)->pluck('slug')->all();
            foreach ($static as $name) {
                $url = route($name);
                if (! in_array(trim(parse_url($url, PHP_URL_PATH), '/'), $inactive, true)) {
                    $add($url, null, in_array($name, ['donate', 'events.index', 'blog.index', 'campaigns.index']) ? 'weekly' : 'monthly',
                        in_array($name, ['donate', 'programs.index', 'about']) ? '0.9' : '0.7');
                }
            }
            foreach (Program::active()->get() as $m) {
                $add(route('programs.show', $m), $m->updated_at, 'monthly', '0.8');
            }
            foreach (Campaign::active()->get() as $m) {
                $add(route('campaigns.show', $m), $m->updated_at, 'weekly', '0.8');
            }
            foreach (Event::active()->get() as $m) {
                $add(route('events.show', $m), $m->updated_at, 'monthly', '0.6');
            }
            foreach (Post::published()->where('type', '!=', 'media')->get() as $m) {
                $add($m->url(), $m->updated_at, 'monthly', '0.6');
            }
            foreach (SuccessStory::active()->get() as $m) {
                $add(route('stories.show', $m), $m->updated_at, 'monthly', '0.6');
            }

            return view('site.sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots()
    {
        $lines = app()->environment('production')
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /donate/thank-you', 'Allow: /', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
