<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin SIPAP',
            'email' => 'admin@sipap.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'identifier' => 'ADMIN001'
        ]);

        User::create([
            'name' => 'Dosen Pengampu',
            'email' => '13579@dosen.poliban.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'identifier' => '198001012024011001'
        ]);

        User::create([
            'name' => 'Mahasiswa Rajin',
            'email' => 'c050424007@mahasiswa.poliban.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'identifier' => '2100018001'
        ]);
    }
}
