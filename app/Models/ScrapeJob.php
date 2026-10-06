<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScrapeJob extends Model
{
    use HasFactory;

    protected $table = 'scrape_jobs';

    protected $fillable = [
        'title',
        'platform',
        'target_query',
        'limit_requested',
        'total_scraped',
        'positive_count',
        'neutral_count',
        'negative_count',
        'avg_sentiment_score',
        'status',
        'error_message',
    ];

    protected $casts = [
        'limit_requested' => 'integer',
        'total_scraped' => 'integer',
        'positive_count' => 'integer',
        'neutral_count' => 'integer',
        'negative_count' => 'integer',
        'avg_sentiment_score' => 'float',
    ];

    public function feedbacks(): HasMany
    {
        return $this->hasMany(ScrapedFeedback::class, 'scrape_job_id');
    }

    public function getPositivePercentageAttribute(): float
    {
        if ($this->total_scraped <= 0) {
            return 0.0;
        }
        return round(($this->positive_count / $this->total_scraped) * 100, 1);
    }

    public function getNeutralPercentageAttribute(): float
    {
        if ($this->total_scraped <= 0) {
            return 0.0;
        }
        return round(($this->neutral_count / $this->total_scraped) * 100, 1);
    }

    public function getNegativePercentageAttribute(): float
    {
        if ($this->total_scraped <= 0) {
            return 0.0;
        }
        return round(($this->negative_count / $this->total_scraped) * 100, 1);
    }

    public function getDominantSentimentAttribute(): string
    {
        if ($this->positive_count >= $this->neutral_count && $this->positive_count >= $this->negative_count && $this->positive_count > 0) {
            return 'positive';
        }
        if ($this->negative_count >= $this->positive_count && $this->negative_count >= $this->neutral_count && $this->negative_count > 0) {
            return 'negative';
        }
        return 'neutral';
    }
}
