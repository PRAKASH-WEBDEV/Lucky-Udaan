<?php

use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

/*
| Public website routes. Dashboard routes live in routes/admin.php under /admin.
*/

Route::get('/', Site\HomeController::class)->name('home');

Route::controller(Site\PageController::class)->group(function () {
    Route::get('about-us', 'about')->name('about');
    Route::get('our-team', 'team')->name('team');
    Route::get('transparency', 'transparency')->name('transparency');
    Route::get('certifications', 'certifications')->name('certifications');
    Route::get('annual-reports', 'reports')->name('reports');
    Route::get('gallery', 'gallery')->name('gallery');
    Route::get('media', 'media')->name('media');
    Route::get('faq', 'faq')->name('faq');
    Route::get('testimonials', 'testimonials')->name('testimonials');
    Route::get('privacy-policy', 'policy')->defaults('slug', 'privacy-policy')->name('privacy');
    Route::get('terms-and-conditions', 'policy')->defaults('slug', 'terms-and-conditions')->name('terms');
    Route::get('refund-policy', 'policy')->defaults('slug', 'refund-policy')->name('refund');
});

Route::controller(Site\ContentController::class)->group(function () {
    Route::get('programs', 'programs')->name('programs.index');
    Route::get('programs/{program:slug}', 'program')->name('programs.show');
    Route::get('campaigns', 'campaigns')->name('campaigns.index');
    Route::get('campaigns/{campaign:slug}', 'campaign')->name('campaigns.show');
    Route::get('events', 'events')->name('events.index');
    Route::get('events/{event:slug}', 'event')->name('events.show');
    Route::get('blog', 'blog')->name('blog.index');
    Route::get('blog/{post:slug}', 'post')->name('blog.show');
    Route::get('announcements', 'announcements')->name('announcements.index');
    Route::get('announcements/{post:slug}', 'post')->name('announcements.show');
    Route::get('press-releases', 'press')->name('press.index');
    Route::get('press-releases/{post:slug}', 'post')->name('press.show');
    Route::get('success-stories', 'stories')->name('stories.index');
    Route::get('success-stories/{story:slug}', 'story')->name('stories.show');
});

Route::controller(Site\FormController::class)->group(function () {
    Route::get('contact-us', 'contact')->name('contact');
    Route::get('volunteer', 'volunteer')->name('volunteer');
    Route::get('membership', 'membership')->name('membership');
    Route::get('careers', 'careers')->name('careers');
    Route::get('internships', 'internships')->name('internships');
    Route::get('partnership', 'partnership')->name('partnership');
    Route::get('donate', 'donate')->name('donate');
    Route::get('donate/thank-you/{receipt}', 'donateThanks')->name('donate.thanks');

    Route::middleware(['throttle:forms', 'honeypot'])->group(function () {
        Route::post('contact-us', 'submitContact')->name('contact.submit');
        Route::post('volunteer', 'submitVolunteer')->name('volunteer.submit');
        Route::post('membership', 'submitMembership')->name('membership.submit');
        Route::post('careers', 'submitApplication')->defaults('kind', 'career')->name('careers.submit');
        Route::post('internships', 'submitApplication')->defaults('kind', 'internship')->name('internships.submit');
        Route::post('partnership', 'submitPartnership')->name('partnership.submit');
        Route::post('donate', 'submitDonation')->name('donate.submit');
    });
});

Route::get('sitemap.xml', Site\SitemapController::class)->name('sitemap');
Route::get('robots.txt', [Site\SitemapController::class, 'robots'])->name('robots');

// Friendly aliases
Route::redirect('about', 'about-us', 301);
Route::redirect('contact', 'contact-us', 301);
Route::redirect('team', 'our-team', 301);
Route::redirect('news', 'blog?type=news', 301);
