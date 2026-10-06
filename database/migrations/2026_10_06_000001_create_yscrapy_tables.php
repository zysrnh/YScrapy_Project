<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scrape_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('platform'); // all, news, youtube, twitter, google_review, custom
            $table->string('target_query');
            $table->integer('limit_requested')->default(20);
            $table->integer('total_scraped')->default(0);
            $table->integer('positive_count')->default(0);
            $table->integer('neutral_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->float('avg_sentiment_score')->default(0.0);
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('scraped_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_job_id')->constrained('scrape_jobs')->onDelete('cascade');
            $table->string('platform');
            $table->string('author_name')->default('Anonim');
            $table->string('author_handle')->nullable();
            $table->text('content_raw');
            $table->text('content_clean')->nullable();
            $table->text('source_url')->nullable();
            $table->string('sentiment_label')->default('neutral'); // positive, neutral, negative
            $table->float('sentiment_score')->default(0.0); // -1.0 s/d 1.0
            $table->json('sentiment_tokens')->nullable(); // array kata kunci pemicu sentimen
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_feedbacks');
        Schema::dropIfExists('scrape_jobs');
    }
};
