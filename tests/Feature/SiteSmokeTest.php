<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Donation;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SiteSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_every_public_page_loads(): void
    {
        $pages = ['/', '/about-us', '/our-team', '/transparency', '/certifications', '/annual-reports', '/gallery', '/media', '/faq',
            '/testimonials', '/privacy-policy', '/terms-and-conditions', '/refund-policy', '/programs', '/programs/healthcare',
            '/campaigns', '/events', '/blog', '/announcements', '/press-releases', '/success-stories', '/contact-us', '/volunteer',
            '/membership', '/careers', '/internships', '/partnership', '/donate', '/sitemap.xml', '/robots.txt'];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
        $this->get('/does-not-exist')->assertNotFound();
    }

    public function test_every_admin_module_loads_for_super_admin(): void
    {
        $admin = Admin::where('email', 'admin@luckyudaanfoundation.com')->firstOrFail();
        $this->actingAs($admin, 'admin');

        foreach (['', 'donations', 'donors', 'campaigns', 'banners', 'programs', 'posts', 'events', 'gallery', 'team', 'pages',
            'testimonials', 'stories', 'faqs', 'certifications', 'reports', 'partners', 'openings', 'volunteers', 'members',
            'enquiries', 'admins', 'roles', 'settings', 'analytics', 'profile', 'programs/create', 'donations/create'] as $uri) {
            $this->get('/admin/'.$uri)->assertOk();
        }
    }

    public function test_module_permissions_are_enforced(): void
    {
        $editor = Admin::where('email', 'editor@luckyudaanfoundation.com')->firstOrFail();
        $this->actingAs($editor, 'admin');
        $this->get('/admin/programs')->assertOk();
        $this->get('/admin/donations')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_contact_form_saves_an_enquiry(): void
    {
        $stamp = Crypt::encryptString((string) (time() - 10)); // rendered 10 seconds ago

        $this->post('/contact-us', [
            '_hp_t' => $stamp, 'name' => 'Test', 'email' => 'test@example.com', 'subject' => 'Hi', 'message' => 'A message long enough to pass.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('enquiries', ['email' => 'test@example.com', 'type' => 'contact']);
    }

    public function test_honeypot_discards_bot_submissions(): void
    {
        $count = Enquiry::count();
        $this->post('/contact-us', [
            '_hp_t' => Crypt::encryptString((string) (time() - 10)), 'website' => 'spam', 'name' => 'Bot', 'email' => 'bot@example.com', 'subject' => 'x', 'message' => 'buy now buy now buy now',
        ])->assertRedirect();
        $this->assertSame($count, Enquiry::count());
    }

    public function test_donation_pledge_is_saved_as_pending(): void
    {
        $this->post('/donate', [
            '_hp_t' => Crypt::encryptString((string) (time() - 10)), 'amount' => 1000, 'name' => 'Donor', 'email' => 'donor@example.com',
            'phone' => '9876543210', 'method' => 'upi', 'consent' => 1,
        ])->assertRedirect();

        $this->assertSame('pending', Donation::latest('id')->first()->status);
    }
}
