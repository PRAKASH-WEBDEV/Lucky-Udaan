<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Beneficiaries, expenses, membership plans and surveys. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('reg_no', 30)->nullable()->unique();
            $table->string('name');
            $table->string('guardian_name')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('id_type', 20)->nullable();
            $table->string('id_number', 40)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('village')->nullable();
            $table->string('block')->nullable();
            $table->string('district')->nullable();
            $table->string('category', 30)->nullable()->index();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('family_size')->nullable();
            $table->decimal('annual_income', 12, 2)->nullable();
            $table->string('support_type')->nullable();
            $table->text('support_details')->nullable();
            $table->date('enrolled_on')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('title');
            $table->string('category', 60)->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('paid_to')->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bill')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('fee', 12, 2)->default(0);
            $table->unsignedSmallInteger('duration_months')->default(12);
            $table->text('benefits')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('questions');
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->json('answers')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['survey_responses', 'surveys', 'membership_plans', 'expenses', 'beneficiaries'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
