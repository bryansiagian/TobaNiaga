<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'umkm_id',
        'kategori_id',
        'status_id',
        'nama',
        'slug',
        'deskripsi',
        'harga',
        'stok',
        'satuan',
        'foto_utama',
        'rating',
        'total_terjual',
    ];

    protected $casts = [
        'harga'        => 'float',
        'stok'         => 'integer',
        'rating'       => 'float',
        'total_terjual'=> 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusProduk::class, 'status_id');
    }

    public function fotoProduk()
    {
        return $this->hasMany(FotoProduk::class, 'produk_id');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'produk_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'produk_id');
    }

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class, 'produk_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produk_id');
    }

    public function promo()
    {
        return $this->belongsToMany(Promo::class, 'promo_produk', 'produk_id', 'promo_id');
    }
}
