<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        Mahasiswa::updateOrCreate([
            'nim' => '1007'
        ], [
            'nama' => 'Mahasiswa Satu',
            'tempat_lahir' => 'Bandung',
            'tgl_lahir' => '2001-01-01',
            'prodi_id' => 1,
            'th_masuk' => 2020,
            'email' => 'mhs1@example.com',
            'password' => Hash::make('passwordmhs'),
            'role' => 'mahasiswa',
            'status' => 1 // 1=aktif, 0=non-aktif
        ]);

        // Contoh mahasiswa non-aktif
        Mahasiswa::updateOrCreate([
            'nim' => '1006'
        ], [
            'nama' => 'Mahasiswa Nonaktif',
            'tempat_lahir' => 'Surabaya',
            'tgl_lahir' => '2000-12-12',
            'prodi_id' => 1,
            'th_masuk' => 2019,
            'email' => 'mhs2@example.com',
            'password' => Hash::make('passwordmhs2'),
            'role' => 'mahasiswa',
            'status' => 0
        ]);
        
        // Tambah mahasiswa lagi untuk testing
        $nimValues = ['1001', '1002', '1003', '1004', '1005'];
        $namaValues = ['BudiSantoso', 'SitiAminah', 'AhmadFauzi', 'DewiLestari', 'RudiHermawan'];
        
        for ($i = 0; $i < count($nimValues); $i++) {
            Mahasiswa::updateOrCreate([
                'nim' => $nimValues[$i]
            ], [
                'nama' => $namaValues[$i],
                'tempat_lahir' => 'Jakarta',
                'tgl_lahir' => '2001-01-' . str_pad($i+1, 2, '0', STR_PAD_LEFT),
                'prodi_id' => 1,
                'th_masuk' => 2021,
                'email' => strtolower($namaValues[$i]) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'mhs',
                'status' => 1
            ]);
        }
    }
}
