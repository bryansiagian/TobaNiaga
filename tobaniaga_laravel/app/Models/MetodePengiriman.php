<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePengiriman extends Model
{
    public $timestamps = false;

    protected $table = 'metode_pengiriman';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'metode_pengiriman_id');
    }
}
