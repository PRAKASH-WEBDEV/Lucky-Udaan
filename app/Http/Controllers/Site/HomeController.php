<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Program;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        $page = Page::where('slug', 'home')->first();
        seo()->fromPage($page);
        seo()->title = $page?->meta_title; // home uses the default full title when empty

        $programs = Program::active()->where('is_featured', true)->ordered()->limit(8)->get();
        if ($programs->count() < 4) {
            $programs = Program::active()->ordered()->limit(8)->get();
        }

        return view('site.home', [
            'banners' => Banner::active()->ordered()->get(),
            'programs' => $programs,
            'programCount' => Program::active()->count(),
            'campaigns' => Campaign::active()->orderByDesc('is_featured')->latest()->limit(3)->get(),
            'events' => Event::upcoming()->limit(3)->get(),
            'posts' => Post::published()->ofType(['blog', 'news'])->limit(3)->get(),
            'testimonials' => Testimonial::active()->ordered()->limit(6)->get(),
            'partners' => Partner::active()->ordered()->get(),
        ]);
    }
}
