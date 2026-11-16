<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    protected $fillable = [
        'booking_id',
        'menu_id',
        'nama_menu',
        'harga_satuan',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'integer',
            'jumlah' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
