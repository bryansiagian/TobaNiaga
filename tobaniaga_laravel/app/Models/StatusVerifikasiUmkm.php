<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusVerifikasiUmkm extends Model
{
    public $timestamps = false;

    protected $table = 'status_verifikasi_umkm';

    protected $fillable = ['kode', 'label'];

    public function umkm()
    {
        return $this->hasMany(Umkm::class, 'status_verifikasi_id');
    }
}
