<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_DIBAYAR = 'dibayar';

    public const STATUS_DIPROSES = 'diproses';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS_KEDALUWARSA = 'kedaluwarsa';

    public const STATUS_GAGAL = 'gagal';

    /** Status yang masih menahan kapasitas slot (PRD bagian 7). */
    public const STATUS_MENAHAN_SLOT = [
        self::STATUS_MENUNGGU_PEMBAYARAN,
        self::STATUS_DIBAYAR,
        self::STATUS_DIPROSES,
    ];

    public const JENIS_COFFEESHOP = Menu::JENIS_COFFEESHOP;

    public const JENIS_CATERING = Menu::JENIS_CATERING;

    protected $fillable = [
        'kode_booking',
        'jenis',
        'nama_pelanggan',
        'no_whatsapp',
        'catatan',
        'tanggal_reservasi',
        'time_slot_id',
        'jumlah_orang',
        'total_harga',
        'status',
        'alasan_pembatalan',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_reservasi' => 'date',
            'jumlah_orang' => 'integer',
            'total_harga' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeMenahanSlot(Builder $query): Builder
    {
        return $query->whereIn('status', self::STATUS_MENAHAN_SLOT);
    }

    public function sudahDibayar(): bool
    {
        return in_array($this->status, [
            self::STATUS_DIBAYAR,
            self::STATUS_DIPROSES,
            self::STATUS_SELESAI,
        ], true);
    }
}
