<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusUmkm extends Model
{
    public $timestamps = false;

    protected $table = 'status_umkm';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function umkm()
    {
        return $this->hasMany(Umkm::class, 'status_id');
    }
}
