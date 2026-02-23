<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // Tampilkan halaman settings
    public function index(Request $request)
    {
        return view('settings.index');
    }

    // Proses ubah password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = null;
        $guard = null;

        // Cek login di guard mana
        if (auth('admin')->check()) {
            $user = auth('admin')->user();
            $guard = 'admin';
        } elseif (auth('staff')->check()) {
            $user = auth('staff')->user();
            $guard = 'staff';
        } elseif (auth('mahasiswas')->check()) {
            $user = auth('mahasiswas')->user();
            $guard = 'mahasiswas';
        }

        if (!$user) {
            return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak cocok.');
        }

        // Update password
        $table = '';
        $idColumn = '';
        
        if ($guard === 'admin') {
            $table = 'admins';
            $idColumn = 'id';
        } elseif ($guard === 'staff') {
            $table = 'staff';
            $idColumn = 'id_staff';
        } elseif ($guard === 'mahasiswas') {
            $table = 'mahasiswas';
            $idColumn = 'nim';
        }

        DB::table($table)->where($idColumn, $user->$idColumn)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
