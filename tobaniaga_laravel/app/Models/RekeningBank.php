<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningBank extends Model
{
    protected $table = 'rekening_bank';

    protected $fillable = [
        'umkm_id',
        'nama_bank',
        'nama_pemilik',
        'no_rekening',
        'is_utama',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function pencairanDana()
    {
        return $this->hasMany(PencairanDana::class, 'rekening_bank_id');
    }
}
