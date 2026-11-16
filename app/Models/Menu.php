<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    public const JENIS_COFFEESHOP = 'coffeeshop';

    public const JENIS_CATERING = 'catering';

    public const HARI = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

    protected $fillable = [
        'jenis',
        'nama',
        'deskripsi',
        'harga',
        'kategori',
        'hari',
        'foto',
        'is_active',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function scopeCoffeeshop(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_COFFEESHOP);
    }

    public function scopeCatering(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_CATERING);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
