<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'user_id', 'schedule_id',
        'subtotal_ticket', 'subtotal_fnb', 'tax_amount', 'total_amount',
        'payment_method', 'payment_status', 'payment_proof',
        'paid_at', 'expires_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ticket' => 'float',
            'subtotal_fnb'    => 'float',
            'tax_amount'      => 'float',
            'total_amount'    => 'float',
            'paid_at'         => 'datetime',
            'expires_at'      => 'datetime',
        ];
    }

    // ──────────────────────── Boot ────────────────────────

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($trx) {
            if (empty($trx->transaction_code)) {
                $trx->transaction_code = self::generateCode();
            }
            if (empty($trx->expires_at)) {
                $trx->expires_at = now()->addMinutes(15);
            }
        });
    }

    // ──────────────────────── Relationships ────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function fnbItems(): HasMany
    {
        return $this->hasMany(TransactionFnbItem::class);
    }

    // ──────────────────────── Static Methods ────────────────────────

    public static function generateCode(): string
    {
        do {
            $code = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }

    // ──────────────────────── Scopes ────────────────────────

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending'   => 'Menunggu Pembayaran',
            'paid'      => 'Lunas',
            'failed'    => 'Gagal',
            'expired'   => 'Kedaluwarsa',
            'refunded'  => 'Dikembalikan',
            default     => ucfirst($this->payment_status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'pending'   => 'yellow',
            'paid'      => 'green',
            'failed'    => 'red',
            'expired'   => 'gray',
            'refunded'  => 'blue',
            default     => 'gray',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'transfer_bank' => 'Transfer Bank',
            'gopay'         => 'GoPay',
            'ovo'           => 'OVO',
            'dana'          => 'DANA',
            'shopee_pay'    => 'ShopeePay',
            'credit_card'   => 'Kartu Kredit',
            'debit_card'    => 'Kartu Debit',
            'cash'          => 'Tunai',
            'qris'          => 'QRIS',
            default         => $this->payment_method ?? '-',
        };
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->payment_status === 'pending' && $this->expires_at && now()->isAfter($this->expires_at);
    }
}
