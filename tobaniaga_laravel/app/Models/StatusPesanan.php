<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPesanan extends Model
{
    public $timestamps = false;

    protected $table = 'status_pesanan';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'status_id');
    }
}
