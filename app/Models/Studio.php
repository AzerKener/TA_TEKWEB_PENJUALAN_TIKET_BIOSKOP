<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'total_rows', 'columns_layout', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ──────────────────────── Relationships ────────────────────────

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class)->orderBy('row_label')->orderBy('seat_number');
    }

    // ──────────────────────── Accessors ────────────────────────

    public function getCapacityAttribute(): int
    {
        return $this->seats()->where('is_active', true)->count();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'regular'   => 'Regular',
            'imax'      => 'IMAX',
            '4dx'       => '4DX',
            'vip'       => 'VIP',
            'premiere'  => 'Premiere',
            default     => ucfirst($this->type),
        };
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'regular'   => 'gray',
            'imax'      => 'blue',
            '4dx'       => 'purple',
            'vip'       => 'yellow',
            'premiere'  => 'red',
            default     => 'gray',
        };
    }
}
