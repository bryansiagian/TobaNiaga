<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriUmkmSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Makanan & Minuman',
            'Kerajinan Tangan',
            'Kain & Tekstil',
            'Pertanian & Perkebunan',
            'Perikanan',
            'Kuliner Khas Toba',
            'Souvenir & Oleh-oleh',
            'Lainnya',
        ];

        foreach ($kategori as $nama) {
            DB::table('kategori_umkm')->insertOrIgnore(['nama' => $nama]);
        }
    }
}
