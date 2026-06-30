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

    public function rekeningBank()
    {
        return $this->hasMany(RekeningBank::class, 'umkm_id');
    }

    public function pencairanDana()
    {
        return $this->hasMany(PencairanDana::class, 'umkm_id');
    }

    // ── Saldo & pesanan eligible pencairan ──────────────────────

    /**
     * Pesanan yang statusnya "selesai", sudah settlement,
     * dan belum pernah masuk pencairan yang masih aktif (diajukan/diproses/selesai).
     */
    public function pesananEligibleDicairkan()
    {
        return $this->pesanan()
            ->whereHas('status', fn($q) => $q->where('kode', 'selesai'))
            ->whereHas('pembayaran.status', fn($q) => $q->where('kode', 'settlement'))
            ->whereDoesntHave('pencairanDanaDetail', function ($q) {
                $q->whereHas('pencairanDana', fn($q2) => $q2->whereIn('status', ['diajukan', 'diproses', 'selesai']));
            });
    }

    public function saldoTersedia(): float
    {
        return (float) $this->pesananEligibleDicairkan()
            ->get()
            ->sum(fn($p) => $p->total_harga - $p->ongkos_kirim);
    }

    public function totalSudahDicairkan(): float
    {
        return (float) $this->pencairanDana()
            ->where('status', 'selesai')
            ->sum('jumlah');
    }

    public function totalSedangDiproses(): float
    {
        return (float) $this->pencairanDana()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->sum('jumlah');
    }
}
