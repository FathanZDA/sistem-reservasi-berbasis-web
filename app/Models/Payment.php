<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SETTLEMENT = 'settlement';

    public const STATUS_CAPTURE = 'capture';

    public const STATUS_DENY = 'deny';

    public const STATUS_EXPIRE = 'expire';

    public const STATUS_CANCEL = 'cancel';

    public const STATUS_FAILURE = 'failure';

    protected $fillable = [
        'booking_id',
        'order_id',
        'transaction_id',
        'snap_token',
        'snap_redirect_url',
        'metode_pembayaran',
        'gross_amount',
        'status',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
