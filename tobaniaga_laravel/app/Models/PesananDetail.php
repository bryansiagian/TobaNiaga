<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';

    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'nama_produk_snapshot',
        'harga_satuan_snapshot',
        'jumlah',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan_snapshot' => 'float',
        'jumlah'                => 'integer',
        'subtotal'              => 'float',
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
