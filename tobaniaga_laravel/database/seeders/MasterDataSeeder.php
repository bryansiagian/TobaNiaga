<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Status User
        DB::table('status_user')->insert([
            ['kode' => 'aktif',    'label' => 'Aktif'],
            ['kode' => 'nonaktif', 'label' => 'Nonaktif'],
        ]);

        // Status Verifikasi UMKM
        DB::table('status_verifikasi_umkm')->insert([
            ['kode' => 'pending',  'label' => 'Menunggu Verifikasi'],
            ['kode' => 'verified', 'label' => 'Terverifikasi'],
            ['kode' => 'rejected', 'label' => 'Ditolak'],
        ]);

        // Status Verifikasi Dokumen untuk Sales & Kurir
        DB::table('status_verifikasi_dokumen')->insert([
            ['kode' => 'pending',  'label' => 'Menunggu Verifikasi'],
            ['kode' => 'verified', 'label' => 'Terverifikasi'],
            ['kode' => 'rejected', 'label' => 'Ditolak'],
        ]);

        // Status UMKM
        DB::table('status_umkm')->insert([
            ['kode' => 'aktif',    'label' => 'Aktif'],
            ['kode' => 'nonaktif', 'label' => 'Nonaktif'],
        ]);

        // Status Produk
        DB::table('status_produk')->insert([
            ['kode' => 'tersedia', 'label' => 'Tersedia'],
            ['kode' => 'habis',    'label' => 'Stok Habis'],
            ['kode' => 'nonaktif', 'label' => 'Nonaktif'],
        ]);

        // Status Promo
        DB::table('status_promo')->insert([
            ['kode' => 'aktif',    'label' => 'Aktif'],
            ['kode' => 'nonaktif', 'label' => 'Nonaktif'],
        ]);

        // Status Pesanan
        DB::table('status_pesanan')->insert([
            ['kode' => 'menunggu_pembayaran', 'label' => 'Menunggu Pembayaran', 'urutan' => 1],
            ['kode' => 'diproses',            'label' => 'Diproses',            'urutan' => 2],
            ['kode' => 'dikirim',             'label' => 'Dikirim',             'urutan' => 3],
            ['kode' => 'selesai',             'label' => 'Selesai',             'urutan' => 4],
            ['kode' => 'batal',               'label' => 'Dibatalkan',          'urutan' => 5],
        ]);

        // Status Pembayaran
        DB::table('status_pembayaran')->insert([
            ['kode' => 'pending',    'label' => 'Menunggu Pembayaran'],
            ['kode' => 'settlement', 'label' => 'Pembayaran Berhasil'],
            ['kode' => 'expire',     'label' => 'Kadaluarsa'],
            ['kode' => 'cancel',     'label' => 'Dibatalkan'],
            ['kode' => 'failed',     'label' => 'Gagal'],
        ]);

        // Status Pengiriman
        DB::table('status_pengiriman')->insert([
            ['kode' => 'menunggu_kurir', 'label' => 'Menunggu Kurir',   'urutan' => 1],
            ['kode' => 'dijemput',       'label' => 'Dijemput Kurir',   'urutan' => 2],
            ['kode' => 'diantar',        'label' => 'Sedang Diantar',   'urutan' => 3],
            ['kode' => 'selesai',        'label' => 'Terkirim',         'urutan' => 4],
        ]);

        // Metode Pengiriman
        DB::table('metode_pengiriman')->insert([
            ['kode' => 'ambil_ditempat', 'label' => 'Ambil di Tempat'],
            ['kode' => 'kurir',          'label' => 'Kurir TobaNiaga'],
        ]);

        // Role (via Spatie — seed lewat RoleSeeder terpisah, ini hanya placeholder)
        // Role yang perlu dibuat: guest (opsional), customer, sales, courier, admin
    }
}
