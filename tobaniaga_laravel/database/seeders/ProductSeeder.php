<?php

namespace Database\Seeders;

use App\Models\FotoProduk;
use App\Models\KategoriProduk;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = Umkm::where('owner_id', 3)->first();

        if (!$umkm) {
            $this->command->warn('UMKM dengan owner_id=2 tidak ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $statusTersediaId = DB::table('status_produk')->where('kode', 'tersedia')->value('id');
        $kategoriIds      = KategoriProduk::pluck('id');

        if ($kategoriIds->isEmpty()) {
            $this->command->warn('Tabel kategori_produk kosong. Jalankan KategoriProdukSeeder terlebih dahulu.');
            return;
        }

        $produkDummy = [
            [
                'nama_produk' => 'Kopi Arabika Lintong 250gr',
                'deskripsi'   => 'Kopi arabika asli dataran tinggi Lintong, disangrai medium roast, aroma kuat dengan after-taste sedikit manis. Cocok untuk diseduh manual brew maupun mesin espresso rumahan.',
                'harga'       => 65000,
                'stok'        => 24,
                'satuan'      => 'pcs',
                'foto'        => 'https://picsum.photos/seed/kopi-lintong/800/800',
            ],
            [
                'nama_produk' => 'Kain Ulos Ragidup Motif Klasik',
                'deskripsi'   => 'Ulos Ragidup tenunan tangan asli, motif klasik dengan warna merah-hitam-putih khas Batak Toba. Digunakan untuk acara adat seperti pernikahan dan kelahiran.',
                'harga'       => 850000,
                'stok'        => 6,
                'satuan'      => 'pcs',
                'foto'        => 'https://picsum.photos/seed/ulos-ragidup/800/800',
            ],
            [
                'nama_produk' => 'Keripik Singkong Pedas Manis',
                'deskripsi'   => 'Keripik singkong renyah dengan bumbu pedas manis khas, diolah dari singkong pilihan petani lokal sekitar Danau Toba. Tanpa pengawet, tahan hingga 1 bulan.',
                'harga'       => 18000,
                'stok'        => 40,
                'satuan'      => 'pcs',
                'foto'        => 'https://picsum.photos/seed/keripik-singkong/800/800',
            ],
            [
                'nama_produk' => 'Ukiran Kayu Gorga Batak Mini',
                'deskripsi'   => 'Ukiran kayu dengan motif Gorga khas Batak, ukuran mini cocok untuk hiasan meja atau cinderamata. Diukir oleh pengrajin lokal menggunakan kayu nangka pilihan.',
                'harga'       => 120000,
                'stok'        => 0,
                'satuan'      => 'pcs',
                'foto'        => 'https://picsum.photos/seed/ukiran-gorga/800/800',
            ],
            [
                'nama_produk' => 'Andaliman Kering 100gr',
                'deskripsi'   => 'Rempah khas Batak dengan aroma citrus dan rasa sedikit menggigit di lidah, sudah dikeringkan dan siap pakai untuk bumbu arsik, saksang, atau masakan khas Toba lainnya.',
                'harga'       => 35000,
                'stok'        => 15,
                'satuan'      => 'pcs',
                'foto'        => 'https://picsum.photos/seed/andaliman/800/800',
            ],
        ];

        $produkList = collect();

        foreach ($produkDummy as $data) {
            $produk = Produk::firstOrCreate(
                [
                    'umkm_id'     => $umkm->id,
                    'nama_produk' => $data['nama_produk'],
                ],
                [
                    'kategori_id' => $kategoriIds->random(),
                    'status_id'   => $statusTersediaId,
                    'slug'        => Str::slug($data['nama_produk']),
                    'deskripsi'   => $data['deskripsi'],
                    'harga'       => $data['harga'],
                    'stok'        => $data['stok'],
                    'satuan'      => $data['satuan'],
                ]
            );

            if ($produk->fotoProduk()->count() === 0) {
                FotoProduk::create([
                    'produk_id' => $produk->id,
                    'url_foto'  => $data['foto'],
                    'urutan'    => 0,
                ]);
            }

            $produkList->push($produk);
        }

        $this->command->info("Berhasil membuat {$produkList->count()} produk dummy untuk UMKM \"{$umkm->nama_umkm}\".");

        $this->seedUlasanDummy($produkList, $umkm);
    }

    private function seedUlasanDummy($produkList, Umkm $umkm): void
    {
        $customer = User::where('email', 'customer@gmail.com')->first();

        if (!$customer) {
            $this->command->warn('User customer@gmail.com tidak ditemukan, skip seeding ulasan.');
            return;
        }

        $statusSelesaiId  = DB::table('status_pesanan')->where('kode', 'selesai')->value('id');
        $metodeAmbilId    = DB::table('metode_pengiriman')->where('kode', 'ambil_ditempat')->value('id');

        if (!$statusSelesaiId || !$metodeAmbilId) {
            $this->command->warn('Master data status_pesanan/metode_pengiriman belum lengkap, skip seeding ulasan.');
            return;
        }

        $komentarDummy = [
            'Barangnya sesuai foto, pengiriman juga cepat. Puas belanja di sini.',
            'Kualitas bagus, harga juga masuk akal. Recommended.',
            'Produknya oke, cuma packaging agak kurang rapi.',
            'Sangat puas, sudah beli kedua kali dan tetap bagus kualitasnya.',
            'Sesuai deskripsi, penjual ramah dan responsif.',
        ];

        // Hanya buat ulasan untuk produk dengan stok > 0 (yang masuk akal "sudah pernah dibeli")
        $produkUntukUlasan = $produkList->filter(fn ($p) => $p->stok > 0)->take(3);

        foreach ($produkUntukUlasan as $i => $produk) {
            $pesanan = Pesanan::firstOrCreate(
                ['no_pesanan' => 'TBN-DUMMY-' . str_pad($produk->id, 4, '0', STR_PAD_LEFT)],
                [
                    'customer_id'           => $customer->id,
                    'umkm_id'               => $umkm->id,
                    'alamat_id'             => null,
                    'metode_pengiriman_id'  => $metodeAmbilId,
                    'ongkos_kirim'          => 0,
                    'total_harga'           => $produk->harga,
                    'status_id'             => $statusSelesaiId,
                    'catatan_customer'      => null,
                ]
            );

            if ($pesanan->wasRecentlyCreated) {
                PesananDetail::create([
                    'pesanan_id'             => $pesanan->id,
                    'produk_id'              => $produk->id,
                    'nama_produk_snapshot'   => $produk->nama_produk,
                    'harga_satuan_snapshot'  => $produk->harga,
                    'jumlah'                 => 1,
                    'subtotal'               => $produk->harga,
                ]);
            }

            $sudahAdaUlasan = Ulasan::where('pesanan_id', $pesanan->id)
                ->where('user_id', $customer->id)
                ->where('produk_id', $produk->id)
                ->exists();

            if (!$sudahAdaUlasan) {
                $tanggalUlasan = now()->subDays(fake()->numberBetween(3, 30));

                $ulasan = new Ulasan([
                    'pesanan_id' => $pesanan->id,
                    'user_id'    => $customer->id,
                    'produk_id'  => $produk->id,
                    'umkm_id'    => $umkm->id,
                    'rating'     => fake()->numberBetween(3, 5),
                    'komentar'   => $komentarDummy[$i % count($komentarDummy)],
                    'foto'       => null,
                    'is_anonim'  => false,
                ]);
                $ulasan->created_at = $tanggalUlasan;
                $ulasan->updated_at = $tanggalUlasan;
                $ulasan->save();
            }
        }

        $this->command->info('Berhasil membuat ulasan dummy untuk produk terpilih.');
    }
}
