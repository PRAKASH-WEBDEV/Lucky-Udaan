<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('pan', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 30)->default('upi'); // upi | bank_transfer | cash | cheque | other
            $table->string('transaction_ref')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending | confirmed | failed
            $table->boolean('wants_80g')->default(false);
            $table->text('message')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('source', 20)->default('website'); // website | offline
            $table->timestamp('donated_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('city')->nullable();
            $table->string('occupation')->nullable();
            $table->string('availability')->nullable();
            $table->json('interests')->nullable();
            $table->text('skills')->nullable();
            $table->text('message')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('new')->index(); // new | approved | rejected
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_no')->nullable()->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('occupation')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city')->nullable();
            $table->string('membership_type', 30)->default('general'); // general | life | student | patron
            $table->text('message')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending | approved | rejected
            $table->date('valid_till')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index(); // contact | partnership | career | internship
            $table->foreignId('opening_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('organisation')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status', 20)->default('new')->index(); // new | read | replied | closed
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['enquiries', 'members', 'volunteers', 'donations', 'donors'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
