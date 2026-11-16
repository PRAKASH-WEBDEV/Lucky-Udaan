<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_can_sign_in_with_the_login_form(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->post('/admin/login', ['email' => 'admin@luckyudaanfoundation.com', 'password' => 'LuckyUdaan@2026', 'remember' => '1'])
            ->assertSessionHasNoErrors()->assertRedirect('/admin');
        $this->assertAuthenticated('admin');
        $this->get('/admin')->assertOk();
    }

    public function test_email_is_case_insensitive_and_wrong_password_is_rejected(): void
    {
        $this->post('/admin/login', ['email' => ' Admin@LuckyUdaanFoundation.com ', 'password' => 'LuckyUdaan@2026'])->assertRedirect('/admin');
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'admin@luckyudaanfoundation.com', 'password' => 'wrong-password'])->assertSessionHasErrors('email');
    }
}
