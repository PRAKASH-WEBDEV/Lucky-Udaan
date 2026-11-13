<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Post;
use App\Models\Program;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Listing + detail pages for database-driven content. */
class ContentController extends Controller
{
    public function programs()
    {
        return view('site.programs.index', [
            'page' => PageController::page('programs', 'Our Programs'),
            'items' => Program::active()->ordered()->get(),
        ]);
    }

    public function program(Program $program)
    {
        abort_unless($program->is_active, 404);
        $this->detailSeo($program->meta_title ?: $program->title.' in '.setting('city', 'Gonda').', '.setting('state', 'Uttar Pradesh'), $program->meta_description ?: $program->excerpt, $program->image,
            [['Programs', route('programs.index')], [$program->title, null]]);

        return view('site.programs.show', [
            'program' => $program,
            'campaigns' => $program->campaigns()->active()->latest()->limit(3)->get(),
            'others' => Program::active()->whereKeyNot($program->id)->ordered()->limit(4)->get(),
        ]);
    }

    public function campaigns()
    {
        return view('site.campaigns.index', [
            'page' => PageController::page('campaigns', 'Campaigns'),
            'items' => Campaign::active()->orderByDesc('is_featured')->latest()->paginate(9),
        ]);
    }

    public function campaign(Campaign $campaign)
    {
        abort_unless($campaign->is_active, 404);
        $this->detailSeo($campaign->meta_title ?: $campaign->title, $campaign->meta_description ?: $campaign->excerpt, $campaign->image,
            [['Campaigns', route('campaigns.index')], [$campaign->title, null]]);

        return view('site.campaigns.show', [
            'campaign' => $campaign->load('program'),
            'others' => Campaign::active()->whereKeyNot($campaign->id)->latest()->limit(3)->get(),
        ]);
    }

    public function events(Request $request)
    {
        $past = $request->get('show') === 'past';

        return view('site.events.index', [
            'page' => PageController::page('events', 'Events'),
            'past' => $past,
            'items' => ($past ? Event::past() : Event::upcoming())->paginate(9)->withQueryString(),
        ]);
    }

    public function event(Event $event)
    {
        abort_unless($event->is_active, 404);
        $this->detailSeo($event->meta_title ?: $event->title, $event->meta_description ?: $event->excerpt, $event->image,
            [['Events', route('events.index')], [$event->title, null]]);
        seo()->schema(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'description' => $event->excerpt,
            'startDate' => $event->starts_at->toIso8601String(),
            'endDate' => $event->ends_at?->toIso8601String(),
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'image' => $event->image ? [upload_url($event->image)] : null,
            'location' => ['@type' => 'Place', 'name' => $event->venue ?: setting('city'), 'address' => [
                '@type' => 'PostalAddress', 'addressLocality' => setting('city'), 'addressRegion' => setting('state'), 'addressCountry' => 'IN']],
            'organizer' => ['@type' => 'NGO', 'name' => setting('site_name'), 'url' => url('/')],
        ]));

        return view('site.events.show', [
            'event' => $event,
            'others' => Event::upcoming()->whereKeyNot($event->id)->limit(3)->get(),
        ]);
    }

    public function blog(Request $request)
    {
        $type = in_array($request->get('type'), ['blog', 'news'], true) ? $request->get('type') : null;
        $q = trim((string) $request->get('q'));

        return view('site.posts.index', [
            'page' => PageController::page('blog', 'Blog & News'),
            'type' => $type,
            'q' => $q,
            'tabs' => ['' => 'All', 'blog' => 'Blog', 'news' => 'News'],
            'baseRoute' => 'blog.index',
            'items' => Post::published()->ofType($type ?: ['blog', 'news'])
                ->when($q, fn ($w) => $w->where(fn ($x) => $x->where('title', 'like', "%$q%")->orWhere('excerpt', 'like', "%$q%")))
                ->paginate(9)->withQueryString(),
        ]);
    }

    public function announcements()
    {
        return view('site.posts.index', [
            'page' => PageController::page('announcements', 'Announcements'),
            'items' => Post::published()->ofType('announcement')->paginate(10),
            'compact' => true,
            'baseRoute' => 'announcements.index',
        ]);
    }

    public function press()
    {
        return view('site.posts.index', [
            'page' => PageController::page('press-releases', 'Press Releases'),
            'items' => Post::published()->ofType('press')->paginate(10),
            'compact' => true,
            'baseRoute' => 'press.index',
        ]);
    }

    public function post(Request $request, Post $post)
    {
        abort_unless($post->is_active && (! $post->published_at || $post->published_at->isPast()), 404);
        // Keep one canonical URL per post type.
        if ($post->type !== 'media' && $request->url() !== $post->url()) {
            return redirect()->to($post->url(), 301);
        }

        [$listLabel, $listRoute] = match ($post->type) {
            'announcement' => ['Announcements', 'announcements.index'],
            'press' => ['Press Releases', 'press.index'],
            'media' => ['Media', 'media'],
            default => ['Blog & News', 'blog.index'],
        };
        $this->detailSeo($post->meta_title ?: $post->title, $post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags((string) $post->body), 160)), $post->image,
            [[$listLabel, route($listRoute)], [$post->title, null]], 'article');
        seo()->schema(array_filter([
            '@context' => 'https://schema.org',
            '@type' => $post->type === 'press' || $post->type === 'news' ? 'NewsArticle' : 'BlogPosting',
            'headline' => Str::limit($post->title, 110),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'image' => $post->image ? [upload_url($post->image)] : null,
            'author' => ['@type' => $post->author ? 'Person' : 'Organization', 'name' => $post->author ?: setting('site_name')],
            'publisher' => ['@type' => 'Organization', 'name' => setting('site_name'), 'logo' => ['@type' => 'ImageObject', 'url' => upload_url(setting('logo'))]],
            'mainEntityOfPage' => $post->url(),
        ]));

        return view('site.posts.show', [
            'post' => $post,
            'listLabel' => $listLabel,
            'listRoute' => $listRoute,
            'related' => Post::published()->ofType($post->type)->whereKeyNot($post->id)->limit(3)->get(),
        ]);
    }

    public function stories()
    {
        return view('site.stories.index', [
            'page' => PageController::page('success-stories', 'Success Stories'),
            'items' => SuccessStory::active()->orderByDesc('is_featured')->latest()->paginate(9),
        ]);
    }

    public function story(SuccessStory $story)
    {
        abort_unless($story->is_active, 404);
        $this->detailSeo($story->title, $story->excerpt, $story->image, [['Success Stories', route('stories.index')], [$story->title, null]], 'article');

        return view('site.stories.show', [
            'story' => $story,
            'others' => SuccessStory::active()->whereKeyNot($story->id)->latest()->limit(3)->get(),
        ]);
    }

    private function detailSeo(string $title, ?string $description, ?string $image, array $crumbs, string $type = 'website'): void
    {
        seo()->set($title, $description, $image ? upload_url($image) : null, $type)->breadcrumbs($crumbs);
    }
}
