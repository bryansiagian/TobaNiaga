<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'no_pesanan',
        'customer_id',
        'umkm_id',
        'alamat_id',
        'metode_pengiriman_id',
        'promo_id',
        'ongkos_kirim',
        'diskon',
        'total_harga',
        'status_id',
        'catatan_customer',
    ];

    protected $casts = [
        'ongkos_kirim' => 'float',
        'diskon'       => 'float',
        'total_harga'  => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function alamat()
    {
        return $this->belongsTo(AlamatCustomer::class, 'alamat_id');
    }

    public function metodePengiriman()
    {
        return $this->belongsTo(MetodePengiriman::class, 'metode_pengiriman_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusPesanan::class, 'status_id');
    }

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'pesanan_id');
    }
}
