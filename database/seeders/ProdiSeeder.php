<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    public function run()
    {
        // Use DB directly - insert without singkatan first to debug
        DB::table('prodis')->insertOrIgnore([
            ['kode_prodi' => '1', 'nama_prodi' => 'Teknik Informatika'],
            ['kode_prodi' => '2', 'nama_prodi' => 'Sistem Informasi'],
            ['kode_prodi' => '3', 'nama_prodi' => 'Teknik Komputer'],
        ]);
    }
}
