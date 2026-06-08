<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbItem extends Model
{
    use HasFactory;

    protected $table = 'fnb_items';

    protected $fillable = [
        'name', 'description', 'category', 'price', 'image', 'stock', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'float',
            'is_available' => 'boolean',
        ];
    }

    // ──────────────────────── Relationships ────────────────────────

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionFnbItem::class);
    }

    // ──────────────────────── Scopes ────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-fnb.jpg');
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'food'   => 'Makanan',
            'drink'  => 'Minuman',
            'combo'  => 'Paket Combo',
            'snack'  => 'Snack',
            default  => ucfirst($this->category),
        };
    }
}
