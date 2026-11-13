<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
| Dashboard routes — loaded with the "admin." name prefix and "/admin" URL prefix (see bootstrap/app.php).
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->name('login.attempt');
    Route::get('forgot-password', [Admin\AuthController::class, 'showForgot'])->name('password.request');
    Route::post('forgot-password', [Admin\AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [Admin\AuthController::class, 'showReset'])->name('password.reset');
    Route::post('reset-password', [Admin\AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth:admin', 'admin.active'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile');
    Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

    // [uri, controller, permission module]
    $resources = [
        ['donations', Admin\DonationController::class, 'donations'],
        ['donors', Admin\DonorController::class, 'donors'],
        ['campaigns', Admin\CampaignController::class, 'campaigns'],
        ['banners', Admin\BannerController::class, 'banners'],
        ['programs', Admin\ProgramController::class, 'programs'],
        ['posts', Admin\PostController::class, 'posts'],
        ['events', Admin\EventController::class, 'events'],
        ['gallery', Admin\GalleryController::class, 'gallery'],
        ['team', Admin\TeamController::class, 'team'],
        ['pages', Admin\PageController::class, 'content'],
        ['testimonials', Admin\TestimonialController::class, 'content'],
        ['stories', Admin\StoryController::class, 'content'],
        ['faqs', Admin\FaqController::class, 'content'],
        ['certifications', Admin\CertificationController::class, 'content'],
        ['reports', Admin\ReportController::class, 'content'],
        ['partners', Admin\PartnerController::class, 'content'],
        ['openings', Admin\OpeningController::class, 'content'],
        ['volunteers', Admin\VolunteerController::class, 'volunteers'],
        ['members', Admin\MemberController::class, 'members'],
        ['enquiries', Admin\EnquiryController::class, 'enquiries'],
        ['admins', Admin\AdminUserController::class, 'admins'],
        ['roles', Admin\RoleController::class, 'admins'],
    ];

    foreach ($resources as [$uri, $controller, $module]) {
        Route::middleware("admin.can:$module")->group(function () use ($uri, $controller) {
            Route::get("$uri/export", [$controller, 'export'])->name("$uri.export");
            Route::patch("$uri/{id}/toggle", [$controller, 'toggle'])->whereNumber('id')->name("$uri.toggle");
            Route::resource($uri, $controller)->parameters([$uri => 'id'])->where(['id' => '[0-9]+']);
        });
    }

    Route::middleware('admin.can:donations')->get('donations/{id}/receipt', [Admin\DonationController::class, 'receipt'])->whereNumber('id')->name('donations.receipt');
    Route::middleware('admin.can:enquiries')->get('enquiries/{id}/attachment', [Admin\EnquiryController::class, 'attachment'])->whereNumber('id')->name('enquiries.attachment');

    Route::middleware('admin.can:settings')->group(function () {
        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('admin.can:analytics')->group(function () {
        Route::get('analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export/{format}', [Admin\AnalyticsController::class, 'export'])->name('analytics.export');
    });
});
