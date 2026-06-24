<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'email',
        'kode',
        'kadaluarsa_at',
        'digunakan',
    ];

    protected $casts = [
        'kadaluarsa_at' => 'datetime',
        'digunakan'     => 'boolean',
    ];

    /**
     * Cek apakah OTP masih valid (belum digunakan dan belum kadaluarsa).
     */
    public function masihValid(): bool
    {
        return ! $this->digunakan && $this->kadaluarsa_at->isFuture();
    }
}
