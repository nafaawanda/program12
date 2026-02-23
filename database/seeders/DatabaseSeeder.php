<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProdiSeeder::class,
            BukuSeeder::class,
            AdminSeeder::class,
            MahasiswaSeeder::class,
        ]);
    }
}
