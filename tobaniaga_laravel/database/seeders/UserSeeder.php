<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $statusAktifId = DB::table('status_user')
            ->where('kode', 'aktif')
            ->value('id');

        $users = [
            [
                'nama' => 'Administrator',
                'email' => 'admin@gmail.com',
                'no_hp' => '081234567890',
                'role' => 'admin',
            ],
            [
                'nama' => 'Administrator 2',
                'email' => 'admin2@gmail.com',
                'no_hp' => '081234567890',
                'role' => 'admin',
            ],
            [
                'nama' => 'Sales User',
                'email' => 'sales@gmail.com',
                'no_hp' => '081234567891',
                'role' => 'sales',
            ],
            [
                'nama' => 'Courier User',
                'email' => 'courier@gmail.com',
                'no_hp' => '081234567892',
                'role' => 'courier',
            ],
            [
                'nama' => 'Customer User',
                'email' => 'customer@gmail.com',
                'no_hp' => '081234567893',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'password'          => Hash::make('password'),
                    'status_id'         => $statusAktifId,
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($role);

            // Buat UMKM untuk sales jika belum ada
            if ($role === 'sales' && !$user->umkm) {
                $statusVerifId = DB::table('status_verifikasi_umkm')->where('kode', 'pending')->value('id');
                $statusUmkmId  = DB::table('status_umkm')->where('kode', 'aktif')->value('id');
                $kategoriId    = DB::table('kategori_umkm')->value('id'); // ambil kategori pertama

                \App\Models\Umkm::create([
                    'owner_id'             => $user->id,
                    'kategori_id'          => $kategoriId,
                    'nama_umkm'            => 'UMKM ' . $user->nama,
                    'slug'                 => \Illuminate\Support\Str::slug('umkm-' . $user->id),
                    'deskripsi'            => 'Deskripsi UMKM ' . $user->nama,
                    'alamat'               => 'Jl. Contoh No. 1',
                    'provinsi'             => 'Sumatera Utara',
                    'kabupaten'            => 'Kabupaten Toba',
                    'kecamatan'            => '',
                    'desa'                 => '',
                    'no_hp_wa'             => $user->no_hp,
                    'status_verifikasi_id' => $statusVerifId,
                    'status_id'            => $statusUmkmId,
                ]);
            }
        }
    }
}
