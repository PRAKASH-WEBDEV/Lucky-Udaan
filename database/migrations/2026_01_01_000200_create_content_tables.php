<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle', 500)->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->string('subtitle', 500)->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('button2_text')->nullable();
            $table->string('button2_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon', 60)->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->longText('description')->nullable();
            $table->text('highlights')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('goal_amount', 12, 2)->default(0);
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('blog')->index(); // blog | news | announcement | press | media
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('source')->nullable();
            $table->string('external_url')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('venue')->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->nullable();
            $table->string('registration_link')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 60)->nullable()->index();
            $table->string('type', 10)->default('photo'); // photo | video
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('group', 30)->default('team'); // board | team | advisor
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->text('message');
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('success_stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('person_name')->nullable();
            $table->string('location')->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 60)->default('General');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('number')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('valid_till')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('file')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 30)->default('annual'); // annual | financial | audit | impact
            $table->string('year', 20)->nullable();
            $table->text('summary')->nullable();
            $table->string('cover')->nullable();
            $table->string('file')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->string('type', 40)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('openings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('job'); // job | internship
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('duration')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['openings', 'partners', 'reports', 'certifications', 'faqs', 'success_stories', 'testimonials',
            'team_members', 'gallery_items', 'events', 'posts', 'campaigns', 'programs', 'banners', 'pages'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
