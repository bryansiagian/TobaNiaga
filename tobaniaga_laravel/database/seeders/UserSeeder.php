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
                    'password' => Hash::make('password'),
                    'status_id' => $statusAktifId,
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($role);
        }
    }
}
