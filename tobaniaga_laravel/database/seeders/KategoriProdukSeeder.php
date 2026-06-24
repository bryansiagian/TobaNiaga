<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Makanan Ringan',
            'Minuman',
            'Kain Ulos',
            'Anyaman & Kerajinan',
            'Rempah & Bumbu',
            'Ikan & Hasil Laut',
            'Kopi & Teh',
            'Souvenir & Oleh-oleh',
            'Pertanian & Perkebunan',
            'Lainnya',
        ];

        foreach ($kategori as $nama) {
            DB::table('kategori_produk')->insertOrIgnore(['nama' => $nama]);
        }
    }
}
