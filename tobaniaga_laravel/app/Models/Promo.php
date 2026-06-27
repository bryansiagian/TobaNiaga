<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $fillable = [
        'umkm_id',
        'kode',
        'nama_promo',
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

    public function umkmTarget()
    {
        return $this->belongsToMany(Umkm::class, 'promo_umkm', 'promo_id', 'umkm_id');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'promo_produk', 'promo_id', 'produk_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'promo_id');
    }

    public function isValid(float $totalBelanja, int $umkmId): bool
    {
        if (!$this->is_aktif) return false;

        $today = now()->toDateString();
        if ($today < $this->berlaku_mulai->toDateString()) return false;
        if ($today > $this->berlaku_sampai->toDateString()) return false;

        if ($this->kuota !== null && $this->terpakai >= $this->kuota) return false;

        if ($totalBelanja < $this->min_belanja) return false;

        // Promo milik sales — harus cocok UMKM-nya
        if ($this->umkm_id !== null && $this->umkm_id !== $umkmId) return false;

        // Promo admin dengan target UMKM tertentu
        if ($this->umkm_id === null && $this->umkmTarget()->exists()) {
            if (!$this->umkmTarget()->where('umkm_id', $umkmId)->exists()) return false;
        }

        // umkm_id null + tidak ada target = platform-wide, selalu lolos

        return true;
    }

    public function hitungDiskon(float $totalBelanja): float
    {
        if ($this->tipe === 'persen') {
            $diskon = $totalBelanja * ($this->nilai / 100);
            if ($this->maks_diskon) {
                $diskon = min($diskon, $this->maks_diskon);
            }
            return $diskon;
        }

        // nominal
        return min($this->nilai, $totalBelanja);
    }


}
