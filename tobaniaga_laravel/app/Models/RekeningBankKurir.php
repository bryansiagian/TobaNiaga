<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningBankKurir extends Model
{
    protected $table = 'rekening_bank_kurir';

    protected $fillable = [
        'courier_id',
        'nama_bank',
        'nama_pemilik',
        'no_rekening',
        'is_utama',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function pencairanDanaKurir()
    {
        return $this->hasMany(PencairanDanaKurir::class, 'rekening_bank_kurir_id');
    }
}
