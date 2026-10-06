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
        Schema::table('scraped_feedbacks', function (Blueprint $table) {
            $table->text('thumbnail_url')->nullable()->after('source_url');
            $table->longText('full_content')->nullable()->after('content_raw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraped_feedbacks', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_url', 'full_content']);
        });
    }
};
