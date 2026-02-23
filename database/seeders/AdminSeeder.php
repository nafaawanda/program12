<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::updateOrCreate([
            'email' => 'admin@example.com'
        ], [
            'nama' => 'Admin',
            'alamat' => 'Jakarta',
            'no_telp' => '08123456789',
            'password' => Hash::make('passwordanda')
        ]);
    }
}
