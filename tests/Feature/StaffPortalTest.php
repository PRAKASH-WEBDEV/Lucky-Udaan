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

    public function test_login_ignores_case_of_stored_email_and_logs_reason(): void
    {
        Employee::create(['name' => 'Rehnuma', 'email' => ' Rehnuma@Example.com', 'password' => 'abcd123456', 'portal_access' => true, 'is_active' => true]);

        \Illuminate\Support\Facades\Log::spy();
        $this->post('/staff/login', ['email' => 'rehnuma@example.com', 'password' => 'wrong12345'])->assertSessionHasErrors('email');
        \Illuminate\Support\Facades\Log::shouldHaveReceived('warning')->withArgs(fn ($m) => str_contains($m, 'password does not match'))->once();

        $this->post('/staff/login', ['email' => 'rehnuma@example.com', 'password' => 'abcd123456'])->assertRedirect('/staff');
        $this->assertAuthenticated('employee');
    }

    public function test_employee_applies_for_leave_and_sees_notices(): void
    {
        $type = \App\Models\LeaveType::create(['name' => 'Casual Leave', 'annual_quota' => 12, 'is_active' => true]);
        \App\Models\StaffNotice::create(['title' => 'Office closed Saturday', 'message' => 'Maintenance work.', 'priority' => 'important']);
        $employee = Employee::create(['name' => 'Asha Devi', 'email' => 'asha@example.com', 'password' => 'Staff1234', 'portal_access' => true, 'is_active' => true]);

        $this->actingAs($employee, 'employee');
        $this->get('/staff')->assertOk()->assertSee('Office timings')->assertSee('Office closed Saturday');
        $this->get('/staff/leave')->assertOk()->assertSee('Casual Leave');

        $from = today()->addDays(3)->toDateString();
        $this->post('/staff/leave', ['leave_type_id' => $type->id, 'from_date' => $from, 'to_date' => $from, 'half_day' => '1', 'reason' => 'Family function'])
            ->assertRedirect('/staff/leave');
        $leave = $employee->leaveRequests()->first();
        $this->assertSame('pending', $leave->status);
        $this->assertEquals(0.5, $leave->days);
        $this->assertDatabaseHas('admin_notifications', ['type' => 'leave']);

        // Overlapping request is refused; a decided request can no longer be cancelled.
        $this->post('/staff/leave', ['leave_type_id' => $type->id, 'from_date' => $from, 'to_date' => $from, 'reason' => 'Again'])->assertSessionHasErrors('from_date');
        $leave->update(['status' => 'approved', 'admin_note' => 'Enjoy']);
        $this->get('/staff/notices')->assertOk()->assertSee('was approved')->assertSee('Maintenance work.');
        $this->delete('/staff/leave/'.$leave->id)->assertNotFound();
    }

    public function test_database_update_button_and_staff_notices_admin(): void
    {
        $admin = Admin::where('email', 'admin@luckyudaanfoundation.com')->first();
        $this->actingAs($admin, 'admin')->get('/admin')->assertOk()->assertDontSee('Database update needed');
        $this->post('/admin/system/update-database')->assertSessionHas('success', 'The database is already up to date.');
        $this->get('/admin/staff-notices')->assertOk();
        $this->post('/admin/staff-notices', ['title' => 'Team meeting', 'priority' => 'normal', 'is_active' => '1'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('staff_notices', ['title' => 'Team meeting']);
    }
}
