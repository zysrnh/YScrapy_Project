<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    use HasFactory;

    protected $table = 'watchlists';

    protected $fillable = [
        'keyword',
        'platform',
        'frequency',
        'limit_per_run',
        'is_active',
        'last_run_at',
        'total_scraped_runs',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'limit_per_run' => 'integer',
        'total_scraped_runs' => 'integer',
    ];

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'hourly' => 'Setiap 1 Jam',
            'every_six_hours' => 'Setiap 6 Jam',
            'daily' => 'Sekali Sehari',
            default => 'Setiap 1 Jam',
        };
    }
}
