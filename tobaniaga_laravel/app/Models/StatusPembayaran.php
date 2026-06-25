<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPembayaran extends Model
{
    protected $table = 'status_pembayaran';

    protected $fillable = ['kode', 'label'];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'status_id');
    }
}
