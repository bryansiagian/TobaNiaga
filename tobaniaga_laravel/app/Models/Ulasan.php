<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // ── Aturan window waktu edit/hapus ──────────────────────────
    //
    // 0–3 jam   : bisa edit & hapus
    // 3–48 jam  : tidak bisa edit, masih bisa hapus
    // >48 jam   : terkunci total (tidak bisa edit/hapus oleh user)

    public function getBisaDieditAttribute(): bool
    {
        return $this->created_at?->diffInHours(now()) < 3;
    }

    public function getBisaDihapusAttribute(): bool
    {
        return $this->created_at?->diffInHours(now()) < 48;
    }

    public function getSudahDieditAttribute(): bool
    {
        return $this->updated_at?->gt($this->created_at);
    }
}
