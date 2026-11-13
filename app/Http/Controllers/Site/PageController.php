<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Program;
use App\Models\Report;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /** Load a CMS page record and apply its SEO + breadcrumb. */
    public static function page(string $slug, string $fallbackTitle): Page
    {
        $page = Page::where('slug', $slug)->first() ?? new Page(['slug' => $slug, 'title' => $fallbackTitle, 'is_active' => true]);
        abort_unless($page->is_active, 404);
        seo()->fromPage($page, $fallbackTitle)->breadcrumbs([[$page->title, null]]);

        return $page;
    }

    public function about()
    {
        return view('site.about', [
            'page' => self::page('about-us', 'About Us'),
            'programs' => Program::active()->ordered()->get(['title', 'slug', 'icon', 'excerpt']),
            'team' => TeamMember::active()->where('group', 'board')->ordered()->limit(4)->get(),
            'partners' => Partner::active()->ordered()->get(),
        ]);
    }

    public function team()
    {
        return view('site.team', [
            'page' => self::page('our-team', 'Our Team'),
            'groups' => TeamMember::active()->ordered()->get()->groupBy('group')
                ->sortBy(fn ($v, $k) => array_search($k, array_keys(TeamMember::GROUPS))),
        ]);
    }

    public function transparency()
    {
        return view('site.transparency', [
            'page' => self::page('transparency', 'Transparency & Accountability'),
            'certifications' => Certification::active()->ordered()->get(),
            'reports' => Report::active()->orderByDesc('year')->limit(6)->get(),
        ]);
    }

    public function certifications()
    {
        return view('site.certifications', [
            'page' => self::page('certifications', 'Certifications & Registrations'),
            'items' => Certification::active()->ordered()->get(),
        ]);
    }

    public function reports()
    {
        return view('site.reports', [
            'page' => self::page('annual-reports', 'Annual Reports'),
            'items' => Report::active()->orderByDesc('year')->orderByDesc('id')->get()->groupBy('type'),
        ]);
    }

    public function gallery(Request $request)
    {
        $categories = GalleryItem::active()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $active = $categories->contains($request->get('album')) ? $request->get('album') : null;

        return view('site.gallery', [
            'page' => self::page('gallery', 'Gallery'),
            'categories' => $categories,
            'active' => $active,
            'items' => GalleryItem::active()->when($active, fn ($q) => $q->where('category', $active))->ordered()->paginate(24)->withQueryString(),
        ]);
    }

    public function media()
    {
        return view('site.media', [
            'page' => self::page('media', 'Media'),
            'coverage' => Post::published()->ofType('media')->paginate(12),
            'videos' => GalleryItem::active()->where('type', 'video')->ordered()->limit(6)->get(),
        ]);
    }

    public function faq()
    {
        $faqs = Faq::active()->ordered()->get();
        seo()->schema([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer],
            ])->values()->all(),
        ]);

        return view('site.faq', [
            'page' => self::page('faq', 'Frequently Asked Questions'),
            'groups' => $faqs->groupBy('category'),
        ]);
    }

    public function testimonials()
    {
        return view('site.testimonials', [
            'page' => self::page('testimonials', 'Testimonials'),
            'items' => Testimonial::active()->ordered()->get(),
        ]);
    }

    public function policy(string $slug)
    {
        return view('site.policy', ['page' => self::page($slug, ucwords(str_replace('-', ' ', $slug)))]);
    }
}
