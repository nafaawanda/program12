<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\mahasiswas;

class AdminMahasiswaSeeder extends Seeder
{
    public function run()
    {
        mahasiswas::updateOrCreate([
            'nim' => '12345678'
        ], [
            'nama' => 'Admin Mahasiswa',
            'tempat_lahir' => 'Jakarta',
            'tgl_lahir' => '2000-01-01',
            'prodi_id' => 1,
            'th_masuk' => 2020,
            'password' => Hash::make('passwordanda'),
            'role' => 'admin'
        ]);
    }
}
