<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDanaKurir extends Model
{
    protected $table = 'pencairan_dana_kurir';

    protected $fillable = [
        'no_pencairan',
        'courier_id',
        'rekening_bank_kurir_id',
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

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function rekeningBankKurir()
    {
        return $this->belongsTo(RekeningBankKurir::class, 'rekening_bank_kurir_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function detail()
    {
        return $this->hasMany(PencairanDanaKurirDetail::class, 'pencairan_dana_kurir_id');
    }

    public function pengiriman()
    {
        return $this->belongsToMany(Pengiriman::class, 'pencairan_dana_kurir_detail', 'pencairan_dana_kurir_id', 'pengiriman_id')
                    ->withPivot('jumlah');
    }

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
