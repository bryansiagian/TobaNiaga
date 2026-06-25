<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    const UPDATED_AT = null;

    protected $fillable = [
        'pesanan_id',
        'midtrans_order_id',
        'midtrans_trans_id',
        'snap_token',
        'metode',
        'jumlah',
        'status_id',
        'raw_response',
        'paid_at',
    ];

    protected $casts = [
        'jumlah'       => 'float',
        'raw_response' => 'array',
        'paid_at'      => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPembayaran::class, 'status_id');
    }
}
