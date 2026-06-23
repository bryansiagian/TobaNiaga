<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusProduk extends Model
{
    public $timestamps = false;

    protected $table = 'status_produk';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function produk()
    {
        return $this->hasMany(Produk::class, 'status_id');
    }
}
