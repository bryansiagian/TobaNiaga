<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'snap_token',
        'metode',
        'jumlah',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payload',
        'dibayar_at',
    ];

    protected $casts = [
        'jumlah'     => 'float',
        'payload'    => 'array',
        'dibayar_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
