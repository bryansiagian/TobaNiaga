<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanLog extends Model
{
    public $timestamps = false;

    protected $table = 'pengiriman_log';

    protected $fillable = [
        'pengiriman_id',
        'status_id',
        'keterangan',
        'lokasi',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'pengiriman_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPengiriman::class, 'status_id');
    }
}
