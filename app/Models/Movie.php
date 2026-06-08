<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'synopsis', 'genre', 'duration', 'rating',
        'director', 'cast', 'poster_image', 'trailer_url', 'language',
        'has_subtitle', 'status', 'release_date', 'end_date',
        'imdb_rating', 'production_company', 'distributor',
    ];

    protected function casts(): array
    {
        return [
            'genre'        => 'array',
            'has_subtitle' => 'boolean',
            'release_date' => 'date',
            'end_date'     => 'date',
            'imdb_rating'  => 'float',
        ];
    }

    // ──────────────────────── Boot ────────────────────────

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($movie) {
            if (empty($movie->slug)) {
                $movie->slug = Str::slug($movie->title);
            }
        });
    }

    // ──────────────────────── Relationships ────────────────────────

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ──────────────────────── Scopes ────────────────────────

    public function scopeNowPlaying($query)
    {
        return $query->where('status', 'now_playing');
    }

    public function scopeComingSoon($query)
    {
        return $query->where('status', 'coming_soon');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['now_playing', 'coming_soon']);
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getPosterUrlAttribute(): string
    {
        if ($this->poster_image) {
            return asset('storage/' . $this->poster_image);
        }
        return asset('images/default-poster.jpg');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function getDurationFormattedAttribute(): string
    {
        $hours   = intdiv($this->duration, 60);
        $minutes = $this->duration % 60;
        return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
    }

    public function getGenreListAttribute(): string
    {
        return is_array($this->genre) ? implode(', ', $this->genre) : $this->genre;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'now_playing'  => 'Sedang Tayang',
            'coming_soon'  => 'Segera Tayang',
            'ended'        => 'Selesai',
            default        => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'now_playing'  => 'green',
            'coming_soon'  => 'blue',
            'ended'        => 'gray',
            default        => 'gray',
        };
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->trailer_url) return null;
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $this->trailer_url, $matches);
        return isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : null;
    }
}
