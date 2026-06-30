<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusVerifikasiDokumen extends Model
{
    protected $table = 'status_verifikasi_dokumen';

    public $timestamps = false;

    protected $fillable = ['kode', 'label'];

    public function users()
    {
        return $this->hasMany(User::class, 'status_verifikasi_dokumen_id');
    }
}
