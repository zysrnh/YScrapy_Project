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

    /**
     * Badge status sentimen bersih & minimalis (Claymorphism palette).
     */
    public function getSentimentBadgeClassAttribute(): string
    {
        return match ($this->sentiment_label) {
            'positive' => 'bg-[#2E7D32]/10 text-[#2E7D32] border border-[#2E7D32]/25',
            'negative' => 'bg-[#C62828]/10 text-[#C62828] border border-[#C62828]/25',
            default => 'bg-[#1D1D1B]/5 text-[#555552] border border-[#D5D5C8]',
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
     * Thumbnail gambar: Utamakan foto asli. Jika ulasan netizen, gunakan avatar dummy minimalis.
     */
    public function getDisplayThumbnailAttribute(): ?string
    {
        if (!empty($this->thumbnail_url) && !str_contains($this->thumbnail_url, 'photo-1585829365295')) {
            return $this->thumbnail_url;
        }

        // Untuk komentar netizen (YouTube, Twitter, Review): gunakan dummy avatar monokrom Veersa
        if (in_array($this->platform, ['youtube', 'twitter', 'google_review'])) {
            $name = urlencode($this->author_name ?: 'User');
            return "https://ui-avatars.com/api/?name={$name}&background=E7E7DD&color=1D1D1B&bold=true&size=128";
        }

        return null;
    }
}
