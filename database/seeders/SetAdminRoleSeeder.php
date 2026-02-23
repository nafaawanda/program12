<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetAdminRoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('admins')->where('email', 'admin@example.com')->update([
            'role' => 'admin',
        ]);
    }
}
