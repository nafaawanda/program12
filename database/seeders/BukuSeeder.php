<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuSeeder extends Seeder
{
    public function run()
    {
        // Get actual column names and types from database
        $columns = DB::getSchemaBuilder()->getColumnListing('bukus');
        
        // Use integer for kode_buku since it's an integer column
        $bukus = [
            ['kode_buku' => 1, 'nama_buku' => 'Pemrograman Laravel untuk Pemula', 'nama_penulis' => 'John Doe', 'penerbit' => 'PT Gramedia', 'th_terbit' => 2023, 'stock' => 5, 'jenis_buku' => 'Teknologi', 'kode_rak' => 'R001'],
            ['kode_buku' => 2, 'nama_buku' => 'Mastering PHP 8', 'nama_penulis' => 'Jane Smith', 'penerbit' => 'PT Elek Media', 'th_terbit' => 2022, 'stock' => 3, 'jenis_buku' => 'Teknologi', 'kode_rak' => 'R001'],
            ['kode_buku' => 3, 'nama_buku' => 'Database Design Fundamentals', 'nama_penulis' => 'Alice Johnson', 'penerbit' => 'PT Informatika', 'th_terbit' => 2021, 'stock' => 4, 'jenis_buku' => 'Teknologi', 'kode_rak' => 'R002'],
            ['kode_buku' => 4, 'nama_buku' => 'Web Development with Vue.js', 'nama_penulis' => 'Bob Williams', 'penerbit' => 'PT Web Indonesia', 'th_terbit' => 2023, 'stock' => 2, 'jenis_buku' => 'Teknologi', 'kode_rak' => 'R001'],
            ['kode_buku' => 5, 'nama_buku' => 'Algorithm and Data Structure', 'nama_penulis' => 'Charlie Brown', 'penerbit' => 'PT Teknologi Nusantara', 'th_terbit' => 2020, 'stock' => 6, 'jenis_buku' => 'Teknologi', 'kode_rak' => 'R003'],
        ];

        foreach ($bukus as $buku) {
            // Filter only columns that exist in the table
            $filtered = array_intersect_key($buku, array_flip($columns));
            DB::table('bukus')->updateOrInsert(
                ['kode_buku' => $buku['kode_buku']],
                $filtered
            );
        }
    }
}
