<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'umkm_id',
        'kategori_id',
        'status_id',
        'nama_produk',
        'slug',
        'deskripsi',
        'harga',
        'stok',
        'satuan',
    ];

    protected $casts = [
        'harga' => 'float',
        'stok'  => 'integer',
    ];

    // ── Auto-generate slug ─────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Produk $produk) {
            if (empty($produk->slug)) {
                $produk->slug = Str::slug($produk->nama_produk);
            }
        });

        static::updating(function (Produk $produk) {
            if ($produk->isDirty('nama_produk')) {
                $produk->slug = Str::slug($produk->nama_produk);
            }
        });
    }

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
        return $this->hasMany(FotoProduk::class, 'produk_id')->orderBy('urutan');
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

    // ── Scopes untuk filter halaman customer.products.index ────

    public function scopeKategoriUmkm(Builder $query, $kategoriUmkmId): Builder
    {
        return $query->whereHas('umkm', function ($q) use ($kategoriUmkmId) {
            $q->where('kategori_id', $kategoriUmkmId);
        });
    }

    public function scopeHargaMin(Builder $query, $harga): Builder
    {
        return $query->where('harga', '>=', $harga);
    }

    public function scopeHargaMaks(Builder $query, $harga): Builder
    {
        return $query->where('harga', '<=', $harga);
    }

    public function scopeTersediaSaja(Builder $query): Builder
    {
        return $query->where('stok', '>', 0);
    }

    // ── Accessor rating rata-rata ───────────────────────────────

    public function getRatingRataRataAttribute(): float
    {
        return round($this->ulasan()->avg('rating') ?? 0, 1);
    }

    public function getJumlahUlasanAttribute(): int
    {
        return $this->ulasan()->count();
    }
}
