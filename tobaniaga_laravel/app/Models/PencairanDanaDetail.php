<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDanaDetail extends Model
{
    protected $table = 'pencairan_dana_detail';

    public $timestamps = false;

    protected $fillable = [
        'pencairan_dana_id',
        'pesanan_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'float',
    ];

    public function pencairanDana()
    {
        return $this->belongsTo(PencairanDana::class, 'pencairan_dana_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
