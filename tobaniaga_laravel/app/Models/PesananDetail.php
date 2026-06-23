<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'nama_produk',
        'harga_satuan',
        'jumlah',
        'subtotal',
        'catatan',
    ];

    protected $casts = [
        'harga_satuan' => 'float',
        'jumlah'       => 'integer',
        'subtotal'     => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
