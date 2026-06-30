<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDana extends Model
{
    protected $table = 'pencairan_dana';

    protected $fillable = [
        'no_pencairan',
        'umkm_id',
        'rekening_bank_id',
        'jumlah',
        'status',
        'catatan_admin',
        'bukti_transfer',
        'diproses_oleh',
        'diproses_at',
        'selesai_at',
    ];

    protected $casts = [
        'jumlah'      => 'float',
        'diproses_at' => 'datetime',
        'selesai_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function rekeningBank()
    {
        return $this->belongsTo(RekeningBank::class, 'rekening_bank_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function detail()
    {
        return $this->hasMany(PencairanDanaDetail::class, 'pencairan_dana_id');
    }

    public function pesanan()
    {
        return $this->belongsToMany(Pesanan::class, 'pencairan_dana_detail', 'pencairan_dana_id', 'pesanan_id')
                    ->withPivot('jumlah');
    }

    // ── Helper status ───────────────────────────────────────────

    public function labelStatus(): string
    {
        return match ($this->status) {
            'diajukan' => 'Menunggu Diproses',
            'diproses' => 'Sedang Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
            default    => $this->status,
        };
    }
}
