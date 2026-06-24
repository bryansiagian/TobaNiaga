<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkm';

    protected $fillable = [
        'owner_id',
        'kategori_id',
        'status_id',
        'status_verifikasi_id',
        'nama_umkm',
        'slug',
        'deskripsi',
        'alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'no_hp_wa',
        'latitude',
        'longitude',
        'foto_profil',
        'foto_banner',
        'catatan_penolakan',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriUmkm::class, 'kategori_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusUmkm::class, 'status_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'umkm_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'umkm_id');
    }

    public function promo()
    {
        return $this->hasMany(Promo::class, 'umkm_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'umkm_id');
    }

    public function statusVerifikasi()
    {
        return $this->belongsTo(StatusVerifikasiUmkm::class, 'status_verifikasi_id');
    }

    public function statusUmkm()
    {
        return $this->belongsTo(StatusUmkm::class, 'status_id');
    }
}
