<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'seat_id', 'schedule_id',
        'ticket_code', 'seat_type', 'price', 'status', 'used_at',
    ];

    protected function casts(): array
    {
        return [
            'price'   => 'float',
            'used_at' => 'datetime',
        ];
    }

    // ──────────────────────── Boot ────────────────────────

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_code)) {
                $ticket->ticket_code = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    // ──────────────────────── Relationships ────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getQrDataAttribute(): string
    {
        return json_encode([
            'ticket_code' => $this->ticket_code,
            'seat'        => $this->seat?->seat_code,
            'schedule_id' => $this->schedule_id,
            'status'      => $this->status,
        ]);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'Aktif',
            'used'      => 'Sudah Digunakan',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }
}
