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

        // Staff management
        ['employees', Admin\EmployeeController::class, 'hr'],
        ['attendance', Admin\AttendanceController::class, 'hr'],
        ['leaves', Admin\LeaveRequestController::class, 'hr'],
        ['leave-types', Admin\LeaveTypeController::class, 'hr'],
        ['holidays', Admin\HolidayController::class, 'hr'],
        ['devices', Admin\HrDeviceController::class, 'hr'],
        ['violations', Admin\HrViolationController::class, 'hr'],
        ['offer-letters', Admin\OfferLetterController::class, 'hr'],
        ['staff-notices', Admin\StaffNoticeController::class, 'hr'],
        ['expense-claims', Admin\ExpenseClaimController::class, 'hr'],
        ['payroll', Admin\PayrollController::class, 'payroll'],
        ['salary-slips', Admin\SalarySlipController::class, 'payroll'],
        ['salary-structures', Admin\SalaryStructureController::class, 'payroll'],

        // Beneficiaries
        ['beneficiaries', Admin\BeneficiaryController::class, 'beneficiaries'],

        // Content
        ['press', Admin\PressReleaseController::class, 'posts'],
        ['announcements', Admin\AnnouncementController::class, 'posts'],
        ['categories', Admin\CategoryController::class, 'posts'],
        ['tags', Admin\TagController::class, 'posts'],
        ['authors', Admin\AuthorController::class, 'posts'],
        ['trust-wall', Admin\TrustWallController::class, 'content'],
        ['newsletter', Admin\NewsletterController::class, 'newsletter'],

        // Website
        ['values', Admin\ValueController::class, 'content'],
        ['journey', Admin\JourneyController::class, 'content'],
        ['awards', Admin\AwardController::class, 'content'],
        ['leaders', Admin\LeaderController::class, 'content'],
        ['social-embeds', Admin\SocialEmbedController::class, 'content'],
        ['translations', Admin\TranslationController::class, 'content'],

        // Finance
        ['expenses', Admin\ExpenseController::class, 'finance'],
        ['membership-plans', Admin\MembershipPlanController::class, 'finance'],

        // Programs & outreach
        ['surveys', Admin\SurveyController::class, 'surveys'],
        ['careers', Admin\CareerController::class, 'content'],
        ['internships', Admin\InternshipController::class, 'content'],

        // Settings
        ['subscriptions', Admin\SubscriptionController::class, 'settings'],
        ['notifications', Admin\NotificationController::class, null],
        ['audit-logs', Admin\AuditLogController::class, 'audit'],
    ];

    foreach ($resources as [$uri, $controller, $module]) {
        Route::middleware($module ? "admin.can:$module" : [])->group(function () use ($uri, $controller) {
            Route::get("$uri/export", [$controller, 'export'])->name("$uri.export");
            Route::patch("$uri/{id}/toggle", [$controller, 'toggle'])->whereNumber('id')->name("$uri.toggle");
            Route::resource($uri, $controller)->parameters([$uri => 'id'])->where(['id' => '[0-9]+']);
        });
    }

    Route::middleware('admin.can:donations')->get('donations/{id}/receipt', [Admin\DonationController::class, 'receipt'])->whereNumber('id')->name('donations.receipt');
    Route::middleware('admin.can:enquiries')->get('enquiries/{id}/attachment', [Admin\EnquiryController::class, 'attachment'])->whereNumber('id')->name('enquiries.attachment');

    Route::post('system/update-database', [Admin\SystemController::class, 'updateDatabase'])->name('system.update-database');
    Route::post('notifications/read-all', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::middleware('admin.can:hr')->group(function () {
        Route::get('hr', [Admin\HrController::class, 'overview'])->name('hr.overview');
        Route::get('hr/letterhead', [Admin\HrController::class, 'letterhead'])->name('hr.letterhead');
        Route::get('offer-letters/{id}/print', [Admin\HrController::class, 'offerLetter'])->whereNumber('id')->name('offer-letters.print');
    });
    Route::middleware('admin.can:payroll')->group(function () {
        Route::post('hr/payroll/generate', [Admin\HrController::class, 'generatePayroll'])->name('hr.payroll.generate');
        Route::get('payroll/{id}/slip', [Admin\HrController::class, 'slip'])->whereNumber('id')->name('payroll.slip');
    });
    Route::middleware('admin.can:surveys')->get('surveys/{id}/responses', [Admin\SurveyController::class, 'responses'])->whereNumber('id')->name('surveys.responses');

    Route::middleware('admin.can:content')->group(function () {
        Route::get('tools/links', [Admin\ToolController::class, 'links'])->name('tools.links');
        Route::get('tools/images', [Admin\ToolController::class, 'images'])->name('tools.images');
        Route::post('tools/images', [Admin\ToolController::class, 'processImage'])->name('tools.images.process');
        Route::get('tools/media', [Admin\ToolController::class, 'media'])->name('tools.media');
        Route::post('tools/media', [Admin\ToolController::class, 'uploadMedia'])->name('tools.media.upload');
        // Shortcut: About page editor.
        Route::get('about-page', fn () => redirect()->route('admin.pages.edit', \App\Models\Page::where('slug', 'about-us')->value('id') ?? abort(404)))->name('about-page');
    });

    Route::middleware('admin.can:settings')->group(function () {
        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('admin.can:analytics')->group(function () {
        Route::get('analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export/{format}', [Admin\AnalyticsController::class, 'export'])->name('analytics.export');
    });
});
