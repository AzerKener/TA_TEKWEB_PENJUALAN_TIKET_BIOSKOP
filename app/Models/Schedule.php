<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'studio_id', 'show_date', 'start_time', 'end_time',
        'price_regular', 'price_vip', 'price_couple', 'language_type', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'show_date'     => 'date',
            'price_regular' => 'float',
            'price_vip'     => 'float',
            'price_couple'  => 'float',
            'is_active'     => 'boolean',
        ];
    }

    // ──────────────────────── Relationships ────────────────────────

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function seatLocks(): HasMany
    {
        return $this->hasMany(SeatLock::class);
    }

    // ──────────────────────── Scopes ────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('show_date', '>=', today())->where('is_active', true);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('show_date', $date);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getAvailableSeatsCountAttribute(): int
    {
        $totalSeats  = $this->studio->seats()->where('is_active', true)->count();
        $bookedSeats = $this->tickets()->where('status', '!=', 'cancelled')->count();
        return max(0, $totalSeats - $bookedSeats);
    }

    public function getOccupancyRateAttribute(): float
    {
        $totalSeats = $this->studio->seats()->where('is_active', true)->count();
        if ($totalSeats === 0) return 0;
        $bookedSeats = $this->tickets()->where('status', '!=', 'cancelled')->count();
        return round(($bookedSeats / $totalSeats) * 100, 1);
    }

    public function getLanguageTypeLabelAttribute(): string
    {
        return match ($this->language_type) {
            'dubbed'     => 'Sulih Suara',
            'subtitled'  => 'Subtitle',
            'original'   => 'Original',
            default      => ucfirst($this->language_type),
        };
    }

    public function getShowDateFormattedAttribute(): string
    {
        return $this->show_date->translatedFormat('l, d F Y');
    }

    /**
     * Cek apakah jadwal ini konflik dengan jadwal lain di studio yang sama.
     */
    public function hasConflict(): bool
    {
        return Schedule::where('studio_id', $this->studio_id)
            ->where('show_date', $this->show_date)
            ->where('id', '!=', $this->id ?? 0)
            ->where(function ($q) {
                $q->whereBetween('start_time', [$this->start_time, $this->end_time])
                  ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                  ->orWhere(function ($q2) {
                      $q2->where('start_time', '<=', $this->start_time)
                         ->where('end_time', '>=', $this->end_time);
                  });
            })
            ->exists();
    }
}
