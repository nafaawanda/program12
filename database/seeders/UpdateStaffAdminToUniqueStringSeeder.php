<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpdateStaffAdminToUniqueStringSeeder extends Seeder
{
    public function run()
    {
        // 1. Tambah kolom kode_staff dan kode_admin sementara
        if (!Schema::hasColumn('staff', 'kode_staff')) {
            Schema::table('staff', function ($table) {
                $table->string('kode_staff', 20)->nullable();
            });
        }
        if (!Schema::hasColumn('admins', 'kode_admin')) {
            Schema::table('admins', function ($table) {
                $table->string('kode_admin', 20)->nullable();
            });
        }

        // 2. Generate kode unik dan update kolom id_staff dan id di admins
        $staffs = DB::table('staff')->get();
        foreach ($staffs as $staff) {
            $kode = 'STF' . str_pad($staff->id_staff, 3, '0', STR_PAD_LEFT);
            DB::table('staff')->where('id_staff', $staff->id_staff)
                ->update(['kode_staff' => $kode]);
        }
        $admins = DB::table('admins')->get();
        foreach ($admins as $admin) {
            $kode = 'ADM' . str_pad($admin->id, 3, '0', STR_PAD_LEFT);
            DB::table('admins')->where('id', $admin->id)
                ->update(['kode_admin' => $kode]);
        }

        // 3. Update id_staff dan id di admins ke kode unik
        foreach ($staffs as $staff) {
            $kode = DB::table('staff')->where('id_staff', $staff->id_staff)->value('kode_staff');
            DB::table('staff')->where('id_staff', $staff->id_staff)->update(['id_staff' => $kode]);
        }
        foreach ($admins as $admin) {
            $kode = DB::table('admins')->where('id', $admin->id)->value('kode_admin');
            DB::table('admins')->where('id', $admin->id)->update(['id' => $kode]);
        }

        // 4. Update pegawai_id di pinjams ke kode unik
        $pinjams = DB::table('pinjams')->get();
        foreach ($pinjams as $pj) {
            if ($pj->pegawai_type === 'staff') {
                $kode = DB::table('staff')->where('kode_staff', 'STF' . str_pad($pj->pegawai_id, 3, '0', STR_PAD_LEFT))->value('id_staff');
                if (!$kode) $kode = DB::table('staff')->where('id_staff', $pj->pegawai_id)->value('id_staff');
                if ($kode) DB::table('pinjams')->where('id', $pj->id)->update(['pegawai_id' => $kode]);
            } elseif ($pj->pegawai_type === 'admin') {
                $kode = DB::table('admins')->where('kode_admin', 'ADM' . str_pad($pj->pegawai_id, 3, '0', STR_PAD_LEFT))->value('id');
                if (!$kode) $kode = DB::table('admins')->where('id', $pj->pegawai_id)->value('id');
                if ($kode) DB::table('pinjams')->where('id', $pj->id)->update(['pegawai_id' => $kode]);
            } else {
                // Data lama: cek di admin dulu, baru staff
                $kode = DB::table('admins')->where('kode_admin', 'ADM' . str_pad($pj->pegawai_id, 3, '0', STR_PAD_LEFT))->value('id');
                if (!$kode) $kode = DB::table('admins')->where('id', $pj->pegawai_id)->value('id');
                if (!$kode) $kode = DB::table('staff')->where('kode_staff', 'STF' . str_pad($pj->pegawai_id, 3, '0', STR_PAD_LEFT))->value('id_staff');
                if (!$kode) $kode = DB::table('staff')->where('id_staff', $pj->pegawai_id)->value('id_staff');
                if ($kode) DB::table('pinjams')->where('id', $pj->id)->update(['pegawai_id' => $kode]);
            }
        }

        // 5. Hapus kolom sementara jika sudah selesai (opsional, bisa manual)
        // Schema::table('staff', function ($table) { $table->dropColumn('kode_staff'); });
        // Schema::table('admins', function ($table) { $table->dropColumn('kode_admin'); });
    }
}
