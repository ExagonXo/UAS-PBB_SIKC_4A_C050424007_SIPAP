<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alat::create([
            'nama_alat' => 'Proyektor BenQ EX501',
            'deskripsi' => 'Proyektor LCD standar kampus',
            'stok' => 5,
            'status' => 'tersedia'
        ]);

        Alat::create([
            'nama_alat' => 'Pointer Logitech R400',
            'deskripsi' => 'Pointer laser untuk presentasi',
            'stok' => 10,
            'status' => 'tersedia'
        ]);

        Alat::create([
            'nama_alat' => 'HDMI Cable 5m',
            'deskripsi' => 'Kabel HDMI panjang 5 meter',
            'stok' => 8,
            'status' => 'tersedia'
        ]);
    }
}
