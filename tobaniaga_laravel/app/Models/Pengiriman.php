<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengiriman';

    protected $fillable = [
        'pesanan_id',
        'status_id',
        'no_resi',
        'kurir',
        'estimasi_tiba',
        'dikirim_at',
        'diterima_at',
        'catatan',
    ];

    protected $casts = [
        'estimasi_tiba' => 'date',
        'dikirim_at'    => 'datetime',
        'diterima_at'   => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
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
