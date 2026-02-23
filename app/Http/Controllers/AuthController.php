<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('AuthController login dipanggil', ['identity' => $request->identity]);
        $request->validate([
            'identity' => 'required',
            'password' => 'required',
        ]);

        $identity = $request->identity;
        $password = $request->password;
        $remember = $request->filled('remember');

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            // Deteksi sebagai admin, staff, atau mahasiswa (berdasarkan email)
            $credentials = ['email' => $identity, 'password' => $password];
            
            // Cek admin dulu
            $admin = \App\Models\Admin::where('email', $identity)->first();
            if ($admin) {
                Log::info('Coba login admin via email', ['email' => $identity]);
                $result = Auth::guard('admin')->attempt($credentials, $remember);
                if ($result) {
                    $redirect = $request->get('redirect', '/admin/dashboard');
                    return redirect($redirect);
                }
            }
            
            // Cek staff
            if (!$admin || !isset($result) || !$result) {
                $staff = \App\Models\Staff::where('email', $identity)->first();
                if ($staff) {
                    Log::info('Coba login staff via email', ['email' => $identity]);
                    Auth::guard('admin')->logout();
                    $result = Auth::guard('staff')->attempt($credentials, $remember);
                    if ($result) {
                        $redirect = $request->get('redirect', '/staff/dashboard');
                        return redirect($redirect);
                    }
                }
            }
            
            // Cek mahasiswa (jika email tidak ditemukan di admin/staff)
            if ((!isset($admin) || !$admin) || (isset($result) && !$result)) {
                $mahasiswa = \App\Models\mahasiswas::where('email', $identity)->first();
                if ($mahasiswa) {
                    Log::info('Coba login mahasiswa via email', ['email' => $identity]);
                    // Cek status aktif
                    if (!$mahasiswa->status) {
                        return back()->withErrors(['login' => 'Akun mahasiswa tidak aktif. Silakan hubungi administrator.']);
                    }
                    $result = Auth::guard('mahasiswas')->attempt($credentials, $remember);
                    if ($result) {
                        // Cek masa aktif
                        if ($mahasiswa->created_at) {
                            $created = \Carbon\Carbon::parse($mahasiswa->created_at);
                            if ($created->lessThanOrEqualTo(now()->subYears(3))) {
                                $mahasiswa->status = 0;
                                $mahasiswa->save();
                                Auth::guard('mahasiswas')->logout();
                                return back()->withErrors(['login' => 'Akun mahasiswa sudah non aktif karena lebih dari 3 tahun.']);
                            }
                        }
                        $redirect = $request->get('redirect', '/mhs/home');
                        return redirect($redirect);
                    }
                }
            }
            
            return back()->withErrors(['login' => 'Email atau Password salah']);
        } elseif (preg_match('/^(ADM|STF)\d{3,}$/i', $identity)) {
            // Deteksi sebagai ID Admin (ADMxxx) atau Staff (STFxxx)
            // Cek admin
            $admin = \App\Models\Admin::where('id', strtoupper($identity))->first();
            if ($admin) {
                Log::info('Coba login admin via ID', ['id' => $identity]);
                $credentials = ['id' => strtoupper($identity), 'password' => $password];
                $result = Auth::guard('admin')->attempt($credentials, $remember);
                if ($result) {
                    $redirect = $request->get('redirect', '/admin/dashboard');
                    return redirect($redirect);
                }
            }
            
            // Cek staff
            if (!isset($result) || !$result) {
                $staff = \App\Models\Staff::where('id_staff', strtoupper($identity))->first();
                if ($staff) {
                    Log::info('Coba login staff via ID', ['id' => $identity]);
                    Auth::guard('admin')->logout();
                    $credentials = ['id_staff' => strtoupper($identity), 'password' => $password];
                    $result = Auth::guard('staff')->attempt($credentials, $remember);
                    if ($result) {
                        $redirect = $request->get('redirect', '/staff/dashboard');
                        return redirect($redirect);
                    }
                }
            }
            
            return back()->withErrors(['login' => 'ID atau Password salah']);
        } else {
            // Deteksi sebagai mahasiswa (berdasarkan NIM)
            $credentials = ['nim' => $identity, 'password' => $password];
            Log::info('Coba login mahasiswa via NIM', ['nim' => $identity]);
            $mahasiswa = \App\Models\mahasiswas::where('nim', $identity)->first();
            
            if (!$mahasiswa) {
                return back()->withErrors(['login' => 'NIM tidak ditemukan']);
            }
            
            // Cek status non aktif
            if (!$mahasiswa->status) {
                return back()->withErrors(['login' => 'Akun mahasiswa tidak aktif. Silakan hubungi administrator.']);
            }
            
            $result = Auth::guard('mahasiswas')->attempt($credentials, $remember);
            if ($result) {
                // Cek masa aktif
                if ($mahasiswa->created_at) {
                    $created = \Carbon\Carbon::parse($mahasiswa->created_at);
                    if ($created->lessThanOrEqualTo(now()->subYears(3))) {
                        $mahasiswa->status = 0;
                        $mahasiswa->save();
                        Auth::guard('mahasiswas')->logout();
                        return back()->withErrors(['login' => 'Akun mahasiswa sudah non aktif karena lebih dari 3 tahun.']);
                    }
                }
                $redirect = $request->get('redirect', '/mhs/home');
                return redirect($redirect);
            }
            return back()->withErrors(['login' => 'NIM atau Password salah']);
        }
    }
}
