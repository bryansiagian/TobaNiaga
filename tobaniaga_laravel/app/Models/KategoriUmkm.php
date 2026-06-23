<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriUmkm extends Model
{
    public $timestamps = false;

    protected $table = 'kategori_umkm';

    protected $fillable = ['nama'];

    // ── Relationships ──────────────────────────────────────────

    public function umkm()
    {
        return $this->hasMany(Umkm::class, 'kategori_id');
    }
}
