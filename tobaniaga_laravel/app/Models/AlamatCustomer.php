<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlamatCustomer extends Model
{
    protected $table = 'alamat_customer';

    protected $fillable = [
        'user_id',
        'label',
        'nama_penerima',
        'no_hp_penerima',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'kode_pos',
        'alamat_lengkap',
        'is_utama',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'alamat_id');
    }
}
