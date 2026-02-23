<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;

class MahasiswaLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login_mahasiswa');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nim', 'password');
        $remember = $request->filled('remember');
        
        if (Auth::guard('mahasiswas')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/mhs');
        }
        return back()->withErrors(['nim' => 'NIM atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::guard('mahasiswas')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/mahasiswa/login');
    }
}
