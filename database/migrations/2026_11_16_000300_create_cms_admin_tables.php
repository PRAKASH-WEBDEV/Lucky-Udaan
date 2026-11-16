<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Website blocks, blog taxonomy, newsletter, subscriptions, notifications, audit log, translations. */
return new class extends Migration
{
    public function up(): void
    {
        // One table for small repeatable website sections: values, journey, awards, leaders, trust wall, embeds.
        Schema::create('site_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('value', 60)->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->text('embed_code')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('tags')->nullable();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('status', 20)->default('subscribed')->index();
            $table->string('source', 30)->default('website');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('vendor')->nullable();
            $table->string('plan')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('billing_cycle', 20)->default('yearly');
            $table->date('start_date')->nullable();
            $table->date('renewal_date')->nullable()->index();
            $table->string('payment_method', 30)->nullable();
            $table->string('status', 20)->default('active');
            $table->string('invoice')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();
            $table->string('title');
            $table->string('message', 500)->nullable();
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20)->index();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description', 500)->nullable();
            $table->json('changes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group', 60)->default('site');
            $table->string('key', 190)->unique();
            $table->text('en')->nullable();
            $table->text('hi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['translations', 'audit_logs', 'admin_notifications', 'subscriptions', 'newsletter_subscribers'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('tags');
        });
        foreach (['authors', 'tags', 'categories', 'site_blocks'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
