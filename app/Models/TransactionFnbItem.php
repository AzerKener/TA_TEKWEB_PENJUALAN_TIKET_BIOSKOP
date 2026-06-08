<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionFnbItem extends Model
{
    use HasFactory;

    protected $table = 'transaction_fnb_items';

    protected $fillable = [
        'transaction_id', 'fnb_item_id', 'quantity', 'unit_price', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
            'subtotal'   => 'float',
        ];
    }

    // ──────────────────────── Relationships ────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function fnbItem(): BelongsTo
    {
        return $this->belongsTo(FnbItem::class, 'fnb_item_id');
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getSubtotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
