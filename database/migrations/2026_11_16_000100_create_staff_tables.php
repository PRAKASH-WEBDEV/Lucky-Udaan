<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Staff management: employees, attendance, leave, payroll, letters, claims, devices, holidays. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('basic', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('conveyance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('pf_percent', 5, 2)->default(0);
            $table->decimal('esi_percent', 5, 2)->default(0);
            $table->decimal('professional_tax', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 30)->nullable()->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('employment_type', 20)->default('full_time');
            $table->foreignId('salary_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account', 40)->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->string('pan', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('device_user_id', 40)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status', 20)->default('present');
            $table->string('source', 20)->default('manual');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->unsignedSmallInteger('annual_quota')->default(0);
            $table->boolean('is_paid')->default(true);
            $table->boolean('carry_forward')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->nullable()->constrained()->nullOnDelete();
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('days', 5, 1)->default(1);
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('month', 7)->index();
            $table->unsignedSmallInteger('working_days')->default(26);
            $table->decimal('present_days', 5, 1)->default(26);
            $table->decimal('basic', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('gross', 12, 2)->default(0);
            $table->decimal('pf', 12, 2)->default(0);
            $table->decimal('esi', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net', 12, 2)->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->date('paid_on')->nullable();
            $table->string('payment_ref')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'month']);
        });

        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('ctc_annual', 12, 2)->nullable();
            $table->date('issued_on')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('claim_date');
            $table->string('category', 60)->nullable();
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('receipt')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->date('paid_on')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_no', 80)->nullable();
            $table->string('device_type', 30)->default('biometric');
            $table->string('location')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hr_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type', 30)->default('late_arrival');
            $table->text('description')->nullable();
            $table->string('action_taken')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date')->index();
            $table->string('type', 20)->default('festival');
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['holidays', 'hr_violations', 'hr_devices', 'expense_claims', 'offer_letters', 'payrolls',
            'leave_requests', 'leave_types', 'attendances', 'employees', 'salary_structures'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
