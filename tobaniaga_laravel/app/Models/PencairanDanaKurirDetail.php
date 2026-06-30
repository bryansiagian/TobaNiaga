<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDanaKurirDetail extends Model
{
    protected $table = 'pencairan_dana_kurir_detail';

    public $timestamps = false;

    protected $fillable = [
        'pencairan_dana_kurir_id',
        'pengiriman_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'float',
    ];

    public function pencairanDanaKurir()
    {
        return $this->belongsTo(PencairanDanaKurir::class, 'pencairan_dana_kurir_id');
    }

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'pengiriman_id');
    }
}
