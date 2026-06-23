<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $fillable = [
        'umkm_id',
        'kode',
        'nama',
        'deskripsi',
        'tipe',
        'nilai',
        'min_belanja',
        'maks_diskon',
        'kuota',
        'terpakai',
        'berlaku_mulai',
        'berlaku_sampai',
        'is_aktif',
    ];

    protected $casts = [
        'nilai'         => 'float',
        'min_belanja'   => 'float',
        'maks_diskon'   => 'float',
        'kuota'         => 'integer',
        'terpakai'      => 'integer',
        'is_aktif'      => 'boolean',
        'berlaku_mulai' => 'date',
        'berlaku_sampai'=> 'date',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'promo_produk', 'promo_id', 'produk_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'promo_id');
    }
}
