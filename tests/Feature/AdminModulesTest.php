<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Beneficiary;
use App\Models\Employee;
use App\Models\NewsletterSubscriber;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\Setting;
use App\Models\SiteBlock;
use App\Models\Survey;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): Admin
    {
        return Admin::where('email', 'admin@luckyudaanfoundation.com')->firstOrFail();
    }

    public function test_every_new_admin_page_loads(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $resources = ['employees', 'attendance', 'leaves', 'leave-types', 'holidays', 'devices', 'violations', 'offer-letters',
            'expense-claims', 'payroll', 'salary-structures', 'beneficiaries', 'press', 'announcements', 'categories', 'tags',
            'authors', 'trust-wall', 'newsletter', 'values', 'journey', 'awards', 'leaders', 'social-embeds', 'translations',
            'expenses', 'membership-plans', 'surveys', 'careers', 'internships', 'subscriptions'];
        foreach ($resources as $uri) {
            $this->get("/admin/$uri")->assertOk();
            $this->get("/admin/$uri/create")->assertOk();
        }
        foreach (['salary-slips', 'notifications', 'audit-logs', 'hr', 'hr/letterhead', 'tools/links', 'tools/images', 'tools/media',
            'settings?tab=hr', 'settings?tab=letterhead', 'settings?tab=notifications'] as $uri) {
            $this->get("/admin/$uri")->assertOk();
        }
        $this->get('/admin/about-page')->assertRedirect();
        // Every sidebar link resolves.
        $this->get('/admin')->assertOk()->assertSee('Staff Management')->assertSee('Beneficiaries Details')->assertSee('Programs &amp; Outreach', false);
    }

    public function test_attendance_marks_late_and_payroll_calculates_and_prints_slip(): void
    {
        $this->actingAs($this->admin(), 'admin');
        Setting::put(['hr_office_start' => '10:00', 'hr_grace_minutes' => '15']);

        $structure = SalaryStructure::create(['name' => 'Grade B', 'basic' => 10000, 'hra' => 4000, 'conveyance' => 1000, 'other_allowance' => 0, 'pf_percent' => 12, 'esi_percent' => 0, 'professional_tax' => 200]);
        $this->post('/admin/employees', ['name' => 'Asha Devi', 'employee_code' => 'LUF-001', 'employment_type' => 'full_time', 'salary_structure_id' => $structure->id, 'is_active' => 1])
            ->assertRedirect();
        $employee = Employee::where('employee_code', 'LUF-001')->firstOrFail();

        $this->post('/admin/attendance', ['employee_id' => $employee->id, 'date' => today()->format('Y-m-d'), 'check_in' => '10:40', 'status' => 'present', 'source' => 'manual'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('late', Attendance::firstOrFail()->status);

        $this->post('/admin/payroll', ['employee_id' => $employee->id, 'month' => '2026-10', 'working_days' => 26, 'present_days' => 13, 'status' => 'paid', 'paid_on' => '2026-11-01'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $p = Payroll::firstOrFail();
        $this->assertEquals(7500.0, (float) $p->gross);      // (10000+4000+1000) × 13/26
        $this->assertEquals(600.0, (float) $p->pf);          // 12% of pro-rated basic 5000
        $this->assertEquals(6700.0, (float) $p->net);        // 7500 − 600 − 200

        $this->get("/admin/payroll/{$p->id}/slip")->assertOk()->assertSee('SALARY SLIP')->assertSee('6,700.00');
        $this->get('/admin/salary-slips')->assertOk()->assertSee('Asha Devi');

        // Month run skips employees that already have an entry.
        $this->post('/admin/hr/payroll/generate', ['month' => '2026-10', 'working_days' => 26])->assertRedirect();
        $this->assertSame(1, Payroll::count());

        $this->assertTrue(AuditLog::where('action', 'created')->where('subject_type', Employee::class)->exists());
    }

    public function test_beneficiary_gets_registration_number_and_offer_letter_prints(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $this->post('/admin/beneficiaries', ['name' => 'Ramesh', 'category' => 'child', 'is_active' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertMatchesRegularExpression('/^BEN-\d{4}$/', Beneficiary::firstOrFail()->reg_no);

        $this->post('/admin/offer-letters', ['candidate_name' => 'Priya Singh', 'designation' => 'Field Coordinator', 'status' => 'draft', 'ctc_annual' => 240000])->assertRedirect();
        $this->get('/admin/offer-letters/1/print')->assertOk()->assertSee('Field Coordinator')->assertSee('2,40,000', false);
    }

    public function test_public_survey_newsletter_and_notifications(): void
    {
        $survey = Survey::create(['title' => 'Community needs 2026', 'questions' => "What do you need most?\nHow can we help?", 'is_active' => true]);
        $this->get('/surveys/'.$survey->slug)->assertOk()->assertSee('What do you need most?');
        $hp = ['_hp_t' => \Illuminate\Support\Facades\Crypt::encryptString((string) (time() - 10))];
        $this->post('/surveys/'.$survey->slug, $hp + ['answers' => ['Clean water', '']])->assertRedirect()->assertSessionHas('success');
        $this->assertSame(['Clean water', ''], $survey->responses()->first()->answers);

        $this->post('/newsletter', $hp + ['newsletter_email' => 'Friend@Example.com'])->assertRedirect()->assertSessionHas('newsletter');
        $this->assertTrue(NewsletterSubscriber::where('email', 'friend@example.com')->exists());

        $this->assertSame(2, AdminNotification::whereNull('read_at')->count());
        $this->actingAs($this->admin(), 'admin')->get('/admin/notifications')->assertOk()->assertSee('friend@example.com');
        $this->assertSame(0, AdminNotification::whereNull('read_at')->count());
    }

    public function test_website_blocks_render_and_translations_work(): void
    {
        SiteBlock::create(['type' => 'value', 'title' => 'Compassion first', 'description' => 'We listen before we act.']);
        SiteBlock::create(['type' => 'journey', 'title' => 'Foundation registered', 'value' => '2021']);
        SiteBlock::create(['type' => 'trust', 'title' => 'Gonda District Hospital', 'description' => 'Reliable partner.']);
        SiteBlock::create(['type' => 'embed', 'title' => 'Bad', 'embed_code' => '<iframe src="https://evil.example/x"></iframe>']);
        SiteBlock::create(['type' => 'embed', 'title' => 'Video', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);

        $this->get('/about-us')->assertOk()->assertSee('Compassion first')->assertSee('Foundation registered');
        $this->get('/')->assertOk()->assertSee('Gonda District Hospital')->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)->assertDontSee('evil.example');

        Translation::create(['key' => 'home.hello', 'en' => 'Hello', 'hi' => 'नमस्ते']);
        cache()->forget('translations');
        $this->assertSame('Hello', tr('home.hello'));
        app()->setLocale('hi');
        $this->assertSame('नमस्ते', tr('home.hello'));
        $this->assertSame('Fallback', tr('missing.key', 'Fallback'));
    }

    public function test_image_tool_resizes_without_storing(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('GD not available');
        }
        $this->actingAs($this->admin(), 'admin');
        $file = \Illuminate\Http\UploadedFile::fake()->image('photo.jpg', 1600, 900);
        $res = $this->post('/admin/tools/images', ['image' => $file, 'width' => 800, 'format' => 'webp', 'quality' => 80])->assertOk();
        $this->assertSame('image/webp', $res->headers->get('Content-Type'));
        [$w, $h] = getimagesizefromstring($res->getContent());
        $this->assertSame([800, 450], [$w, $h]);
    }
}
