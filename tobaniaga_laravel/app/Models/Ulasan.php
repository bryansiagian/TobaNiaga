<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $fillable = [
        'user_id',
        'umkm_id',
        'produk_id',
        'pesanan_id',
        'rating',
        'komentar',
        'foto',
        'is_anonim',
    ];

    protected $casts = [
        'rating'    => 'integer',
        'foto'      => 'array',
        'is_anonim' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
