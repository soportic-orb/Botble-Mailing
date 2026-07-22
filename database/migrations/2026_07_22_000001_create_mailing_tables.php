<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('mailing_contacts')) {
            Schema::create('mailing_contacts', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->unique();
                $table->string('status', 60)->default('subscribed');
                $table->string('token', 64)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('mailing_campaigns')) {
            Schema::create('mailing_campaigns', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('subject', 400);
                $table->longText('content')->nullable();
                $table->string('type', 60)->default('manual');
                $table->string('status', 60)->default('draft');
                $table->unsignedBigInteger('post_id')->nullable()->index();
                $table->dateTime('scheduled_at')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('last_batch_at')->nullable();
                $table->unsignedInteger('total_recipients')->default(0);
                $table->unsignedInteger('sent_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->unsignedInteger('opened_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('mailing_logs')) {
            Schema::create('mailing_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('campaign_id')->index();
                $table->unsignedBigInteger('contact_id')->nullable()->index();
                $table->string('email');
                $table->string('status', 60)->default('pending');
                $table->dateTime('sent_at')->nullable();
                $table->dateTime('opened_at')->nullable();
                $table->text('error')->nullable();
                $table->string('token', 64)->unique();
                $table->timestamps();

                $table->index(['campaign_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_logs');
        Schema::dropIfExists('mailing_campaigns');
        Schema::dropIfExists('mailing_contacts');
    }
};
