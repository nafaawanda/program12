<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MIDDLEWARE UNTUK AKSES BERDASARKAN ROLE
 * 
 * Middleware ini mengatur akses ke route berdasarkan role user (admins atau mhs)
 * Middleware ini akan:
 * 1. Mengecek apakah user sudah login
 * 2. Mengecek apakah role user sesuai dengan role yang diizinkan
 * 
 * Role yang tersedia:
 * - 'admins': adminsistrator dengan akses penuh
 * - 'mhs': Mahasiswa biasa dengan akses terbatas
 */
class MahasiswaMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Method ini dipanggil setiap kali ada request yang menggunakan middleware ini
     * 
     * @param  \Illuminate\Http\Request  $request Request yang masuk
     * @param  \Closure  $next Closure untuk melanjutkan request ke controller
     * @param  string  ...$role Role yang diizinkan (bisa lebih dari satu: 'admins', 'mhs')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        // ============================================
        Log::info('[DEBUG-MW] Parameter role diterima:', ['role' => $role]);
        // BAGIAN 1: CEK APAKAH USER SUDAH LOGIN
        // ============================================
        // Cek dua guard jika role mengandung 'admin' dan 'mhs'
        $user = null;
        $activeGuard = null;
        
        // Debug: Check all guards
        Log::info('[DEBUG-MW] Guard status:', [
            'admin_check' => Auth::guard('admin')->check(),
            'staff_check' => Auth::guard('staff')->check(),
            'mahasiswas_check' => Auth::guard('mahasiswas')->check(),
        ]);
        
        // BAGIAN 2: CEK ROLE BERDASARKAN PARAMETER
        // ============================================
        // Priority: staff+admin (bukan mhs) harus dicek dulu karena ada case 'staff,admin'
        if ((in_array('admin', $role) || in_array('staff', $role)) && count($role) > 1 && !in_array('mhs', $role)) {
            // Route allows admin OR staff (bukan mhs), check both guards
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $activeGuard = 'admin';
            } elseif (Auth::guard('staff')->check()) {
                $user = Auth::guard('staff')->user();
                $activeGuard = 'staff';
            }
            if (!$user) {
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login terlebih dahulu.');
            }
        } elseif (in_array('mhs', $role) && in_array('admin', $role) && in_array('staff', $role)) {
            // Route allows mhs AND admin AND staff (all three), check all three guards
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $activeGuard = 'admin';
            } elseif (Auth::guard('staff')->check()) {
                $user = Auth::guard('staff')->user();
                $activeGuard = 'staff';
            } elseif (Auth::guard('mahasiswas')->check()) {
                $user = Auth::guard('mahasiswas')->user();
                $activeGuard = 'mahasiswas';
            }
            if (!$user) {
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login terlebih dahulu.');
            }
        } elseif ((in_array('admin', $role) || in_array('mhs', $role)) && count($role) > 1) {
            // Route allows admin OR mhs, check both guards
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $activeGuard = 'admin';
            } elseif (Auth::guard('mahasiswas')->check()) {
                $user = Auth::guard('mahasiswas')->user();
                $activeGuard = 'mahasiswas';
            }
            if (!$user) {
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login terlebih dahulu.');
            }
        } elseif (in_array('admin', $role)) {
            // Hanya admin
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $activeGuard = 'admin';
            } else {
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login sebagai admin.');
            }
        } elseif (in_array('staff', $role)) {
            // Hanya staff
            Log::info('[DEBUG-MW] Checking staff guard');
            if (Auth::guard('staff')->check()) {
                $user = Auth::guard('staff')->user();
                $activeGuard = 'staff';
                Log::info('[DEBUG-MW] Staff authenticated:', ['user' => $user->nama]);
            } else {
                Log::info('[DEBUG-MW] Staff NOT authenticated, redirecting to login');
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login sebagai staff.');
            }
        } elseif (in_array('mhs', $role)) {
            $activeGuard = 'mahasiswas';
            if (!Auth::guard('mahasiswas')->check()) {
                return redirect()->route('login.form')
                    ->with('error', 'Anda harus login terlebih dahulu.');
            }
            $user = Auth::guard('mahasiswas')->user();
        }
        // Log::info('[DEBUG-MW] User login:', ['guard' => $activeGuard, 'user' => $user, 'role_param' => $role]);
        
        // ============================================
        // BAGIAN 3: VALIDASI ROLE USER
        // ============================================
        // Cek apakah role user (admins, mhs, atau staff) ada dalam array role yang diizinkan
        // Untuk Admin dan Mahasiswa, gunakan $user->role
        // Untuk Staff, gunakan guard name karena Staff tidak memiliki role field
        $userRole = null;
        if ($activeGuard === 'staff') {
            $userRole = 'staff';
        } else {
            $userRole = $user->role ?? null;
        }
        
        // Log::info('[DEBUG-MW] Validating role:', ['user_role' => $userRole, 'role_param' => $role]);
        
        if (!in_array($userRole, $role)) {
            Log::info('[DEBUG-MW] Role user tidak sesuai', ['user_role' => $userRole, 'role_param' => $role]);
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');   
        }
        
        // ============================================
        // BAGIAN 4: LANJUTKAN REQUEST
        // ============================================
        // Jika semua validasi berhasil (user sudah login dan role sesuai)
        // Lanjutkan request ke controller dengan memanggil $next($request)
        return $next($request);
    }
}
