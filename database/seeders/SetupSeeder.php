<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mencari user berdasarkan email 'admin@gmail.com'
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Parameter pencarian
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'role' => 'admin',
                'status' => 'active',
            ] // Data yang di-insert / di-update
        );

        // Mencari user berdasarkan email 'karyawan@gmail.com'
        User::updateOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'name' => 'Karyawan',
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'role' => 'user',
                'status' => 'active',
            ]
        );

        // Untuk tabel Setting biasanya hanya ada 1 baris, jadi kita bisa jadikan ID 1 sebagai patokan
        Setting::updateOrCreate(
            ['id' => 1], // Asumsi ID 1 adalah baris pengaturan utama
            [
                'nama_situs' => 'Inventory Management System',
                'logo' => null,
                'favicon' => null,
                'email' => 'inventory@gmail.com',
                'telepon' => '',
                'alamat' => '',
                'auth_background' => null,
                'footer_teks' => 'Inventory Management System',
            ]
        );
    }
}