<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id', 'row_label', 'seat_number', 'seat_code', 'type', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ──────────────────────── Relationships ────────────────────────

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function seatLocks(): HasMany
    {
        return $this->hasMany(SeatLock::class);
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'regular'   => 'Regular',
            'couple'    => 'Couple',
            'vip'       => 'VIP',
            'disabled'  => 'Difabel',
            default     => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'regular'   => '#4CAF50',
            'couple'    => '#E91E63',
            'vip'       => '#FF9800',
            'disabled'  => '#2196F3',
            default     => '#9E9E9E',
        };
    }
}
