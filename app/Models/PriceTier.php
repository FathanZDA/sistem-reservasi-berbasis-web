<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PriceTier extends Model
{
    protected $fillable = [
        'label',
        'min_orang',
        'max_orang',
        'tarif_per_orang',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_orang' => 'integer',
            'max_orang' => 'integer',
            'tarif_per_orang' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Tier aktif yang berlaku untuk jumlah orang tertentu (BR-01, tarif tidak berlapis).
     */
    public static function untukJumlahOrang(int $jumlahOrang): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where('min_orang', '<=', $jumlahOrang)
            ->where(fn (Builder $query) => $query
                ->whereNull('max_orang')
                ->orWhere('max_orang', '>=', $jumlahOrang))
            ->orderBy('min_orang')
            ->first();
    }
}
