<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPengiriman extends Model
{
    public $timestamps = false;

    protected $table = 'status_pengiriman';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function pengiriman()
    {
        return $this->hasMany(Pengiriman::class, 'status_id');
    }

    public function pengirimanLog()
    {
        return $this->hasMany(PengirimanLog::class, 'status_id');
    }
}
