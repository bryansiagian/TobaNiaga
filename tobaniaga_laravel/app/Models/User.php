<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama',
        'email',
        'nik',
        'tanggal_lahir',
        'no_hp',
        'alamat_ktp',
        'foto_ktp',
        'foto_kk',
        'foto_profil',
        'status_verifikasi_dokumen_id',
        'catatan_penolakan_dokumen',
        'password',
        'no_hp',
        'foto_profil',
        'status_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'nik'           => 'encrypted',
        'tanggal_lahir' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function status()
    {
        return $this->belongsTo(StatusUser::class, 'status_id');
    }

    public function statusVerifikasiDokumen()
    {
        return $this->belongsTo(StatusVerifikasiDokumen::class, 'status_verifikasi_dokumen_id');
    }

    public function alamat()
    {
        return $this->hasMany(AlamatCustomer::class, 'user_id');
    }

    public function umkm()
    {
        return $this->hasOne(Umkm::class, 'owner_id');
    }

    public function nikMasked(): ?string
    {
        if (!$this->nik) return null;
        $nik = $this->nik;
        return substr($nik, 0, 4) . str_repeat('*', strlen($nik) - 8) . substr($nik, -4);
    }

    public function dokumenLengkap(): bool
    {
        return !empty($this->nik) && !empty($this->foto_ktp) && !empty($this->foto_kk);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'customer_id');
    }

    // ── Rekening & Pencairan (kurir) ────────────────────────────

    public function rekeningBankKurir()
    {
        return $this->hasMany(RekeningBankKurir::class, 'courier_id');
    }

    public function pencairanDanaKurir()
    {
        return $this->hasMany(PencairanDanaKurir::class, 'courier_id');
    }

    public function pengirimanSelesai()
    {
        return $this->hasMany(Pengiriman::class, 'courier_id');
    }

    /**
     * Pengiriman yang sudah "selesai" dan belum pernah masuk
     * pencairan yang masih aktif (diajukan/diproses/selesai).
     */
    public function pengirimanEligibleDicairkan()
    {
        return $this->pengirimanSelesai()
            ->whereHas('status', fn($q) => $q->where('kode', 'selesai'))
            ->whereDoesntHave('pencairanDanaKurirDetail', function ($q) {
                $q->whereHas('pencairanDanaKurir', fn($q2) => $q2->whereIn('status', ['diajukan', 'diproses', 'selesai']));
            });
    }

    public function saldoTersediaKurir(): float
    {
        return (float) $this->pengirimanEligibleDicairkan()
            ->with('pesanan')
            ->get()
            ->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0);
    }

    public function totalSudahDicairkanKurir(): float
    {
        return (float) $this->pencairanDanaKurir()
            ->where('status', 'selesai')
            ->sum('jumlah');
    }

    public function totalSedangDiprosesKurir(): float
    {
        return (float) $this->pencairanDanaKurir()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->sum('jumlah');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'user_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }
}
