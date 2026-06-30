<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OngkosKirimTrayek extends Model
{
    protected $table = 'ongkos_kirim_trayek';

    protected $fillable = [
        'lokasi_asal',
        'lokasi_tujuan',
        'ongkos',
        'is_aktif',
    ];

    protected $casts = [
        'ongkos'   => 'float',
        'is_aktif' => 'boolean',
    ];

    /**
     * Buat trayek dua arah sekaligus (simetris).
     * Kalau salah satu arah sudah ada, akan di-update; kalau belum, dibuat baru.
     */
    public static function buatSimetris(string $asal, string $tujuan, float $ongkos, bool $isAktif = true): void
    {
        DB::transaction(function () use ($asal, $tujuan, $ongkos, $isAktif) {
            self::updateOrCreate(
                ['lokasi_asal' => $asal, 'lokasi_tujuan' => $tujuan],
                ['ongkos' => $ongkos, 'is_aktif' => $isAktif]
            );

            self::updateOrCreate(
                ['lokasi_asal' => $tujuan, 'lokasi_tujuan' => $asal],
                ['ongkos' => $ongkos, 'is_aktif' => $isAktif]
            );
        });
    }

    /**
     * Cari ongkir antara dua lokasi. Return null kalau trayek belum ada.
     */
    public static function cariOngkos(string $asal, string $tujuan): ?float
    {
        $asal   = trim($asal);
        $tujuan = trim($tujuan);

        if (strcasecmp($asal, $tujuan) === 0) {
            return 0;
        }

        $trayek = self::whereRaw('LOWER(lokasi_asal) = ?', [strtolower($asal)])
            ->whereRaw('LOWER(lokasi_tujuan) = ?', [strtolower($tujuan)])
            ->where('is_aktif', true)
            ->first();

        return $trayek?->ongkos;
    }
}
