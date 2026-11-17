<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPortalTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_sets_login_and_employee_checks_in_and_out(): void
    {
        $admin = Admin::where('email', 'admin@luckyudaanfoundation.com')->first();
        $this->actingAs($admin, 'admin')->post('/admin/employees', [
            'name' => 'Ravi Sharma', 'employee_code' => 'LUF-T1', 'email' => 'Ravi@Example.com', 'employment_type' => 'full_time',
            'portal_access' => '1', 'password' => 'Staff1234', 'password_confirmation' => 'Staff1234', 'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $employee = Employee::where('employee_code', 'LUF-T1')->firstOrFail();
        $this->assertSame('ravi@example.com', $employee->email);
        $this->assertTrue($employee->portal_access);

        auth('admin')->logout();
        $this->get('/staff')->assertRedirect('/staff/login');
        $this->post('/staff/login', ['email' => 'ravi@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/staff/login', ['email' => 'ravi@example.com', 'password' => 'Staff1234'])->assertRedirect('/staff');
        $this->assertAuthenticatedAs($employee, 'employee');
        $this->get('/staff')->assertOk()->assertSee('Check in now');

        $this->post('/staff/check-in')->assertSessionHas('success');
        $this->post('/staff/check-in')->assertSessionHas('error');
        $this->post('/staff/check-out')->assertSessionHas('success');

        $a = $employee->attendances()->first();
        $this->assertNotNull($a->check_in);
        $this->assertNotNull($a->check_out);
        $this->assertSame('web', $a->source);
    }

    public function test_portal_access_required_and_password_required_when_enabled(): void
    {
        $admin = Admin::where('email', 'admin@luckyudaanfoundation.com')->first();
        $this->actingAs($admin, 'admin')->post('/admin/employees', [
            'name' => 'No Pass', 'email' => 'np@example.com', 'employment_type' => 'full_time', 'portal_access' => '1',
        ])->assertSessionHasErrors('password');

        Employee::create(['name' => 'Off', 'email' => 'off@example.com', 'password' => 'Staff1234', 'portal_access' => false]);
        auth('admin')->logout();
        $this->post('/staff/login', ['email' => 'off@example.com', 'password' => 'Staff1234'])->assertSessionHasErrors('email');
    }
}
