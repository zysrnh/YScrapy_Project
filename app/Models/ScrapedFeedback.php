<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapedFeedback extends Model
{
    use HasFactory;

    protected $table = 'scraped_feedbacks';

    protected $fillable = [
        'scrape_job_id',
        'platform',
        'author_name',
        'author_handle',
        'content_raw',
        'full_content',
        'source_url',
        'thumbnail_url',
        'sentiment_label',
        'sentiment_score',
        'sentiment_tokens',
        'scraped_at',
    ];

    protected $casts = [
        'sentiment_score' => 'float',
        'sentiment_tokens' => 'array',
        'scraped_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(ScrapeJob::class, 'scrape_job_id');
    }

    public function getSentimentBadgeClassAttribute(): string
    {
        return match ($this->sentiment_label) {
            'positive' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-[0_0_12px_rgba(16,185,129,0.2)]',
            'negative' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30 shadow-[0_0_12px_rgba(244,63,94,0.2)]',
            default => 'bg-[#9BA8AB]/15 text-[#CCD0CF] border border-[#9BA8AB]/30 shadow-[0_0_12px_rgba(155,168,171,0.15)]',
        };
    }

    public function getSentimentLabelIndoAttribute(): string
    {
        return match ($this->sentiment_label) {
            'positive' => 'Positif',
            'negative' => 'Negatif',
            default => 'Netral',
        };
    }

    /**
     * Fallback gambar jika thumbnail asli kosong.
     */
    public function getDisplayThumbnailAttribute(): string
    {
        if (!empty($this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        return match ($this->platform) {
            'youtube' => 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=300&q=80',
            'news' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=300&q=80',
            'twitter' => 'https://images.unsplash.com/photo-1611605698335-8b1569810432?w=300&q=80',
            'google_review' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=300&q=80',
            default => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=300&q=80',
        };
    }
}
