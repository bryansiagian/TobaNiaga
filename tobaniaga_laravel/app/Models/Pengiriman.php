<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengiriman';

    protected $fillable = [
        'pesanan_id',
        'courier_id',
        'status_id',
        'waktu_pickup',
        'waktu_selesai',
        'catatan_kurir',
        'nama_penerima',
        'relasi_penerima',
        'foto_bukti',
    ];

    protected $casts = [
        'waktu_pickup'  => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPengiriman::class, 'status_id');
    }

    public function log()
    {
        return $this->hasMany(PengirimanLog::class, 'pengiriman_id');
    }
}
