<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProduk extends Model
{
    protected $table = 'produk_foto';

    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'url_foto',
        'urutan',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
