<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('email')->unique();
            $table->string('source'); // 'guide' | 'empty_search'
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('term')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('gclid')->nullable();
            $table->string('referrer')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('brevo_contact_id')->nullable();
            $table->timestamp('last_captured_at')->nullable();
            $table->timestamps();

            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
