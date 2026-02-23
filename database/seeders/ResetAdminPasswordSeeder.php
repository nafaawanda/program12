<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswordSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->where('email', 'admin@example.com')->update([
            'password' => Hash::make('adminanda'),
        ]);
    }
}
