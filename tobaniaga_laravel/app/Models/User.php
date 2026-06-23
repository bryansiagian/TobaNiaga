<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'foto_profil',
        'status_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function status()
    {
        return $this->belongsTo(StatusUser::class, 'status_id');
    }

    public function alamat()
    {
        return $this->hasMany(AlamatCustomer::class, 'user_id');
    }

    public function umkm()
    {
        return $this->hasOne(Umkm::class, 'owner_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'customer_id');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'user_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }
}
