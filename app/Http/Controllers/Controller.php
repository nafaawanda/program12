<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Method index - Menampilkan data mahasiswa
     * 
     * Method ini mengatur akses berdasarkan role:
     * - Admin: bisa melihat semua data mahasiswa
     * - Mahasiswa (mhs): hanya bisa melihat data sendiri
     * 
     * Route ini menggunakan middleware 'AuthMahasiswa:mhs,admin'
     * yang berarti admin dan mahasiswa bisa akses, tapi dengan data yang berbeda
     */
    public function index(Request $request)
    {
        // ============================================
        // BAGIAN 1: AMBIL DATA USER YANG SEDANG LOGIN
        // ============================================
        // $cekRole berisi data user yang sedang login dari tabel 'mahasiswas'
        // Data ini termasuk kolom 'role' yang menentukan akses user
        // Auth::guard('mahasiswas') menggunakan guard khusus untuk autentikasi mahasiswa
        $cekRole = Auth::guard('mahasiswas')->user(); // Ambil data user yang sedang login

        // ============================================
        // BAGIAN 2: AMBIL PARAMETER PENCARIAN
        // ============================================
        $q = trim((string) $request->query('q', ''));

        // ============================================
        // BAGIAN 3: PEMBAGIAN AKSES BERDASARKAN ROLE
        // ============================================
        
        // Hanya admin yang bisa akses, tampilkan semua data mahasiswa
        $mahasiswa = DB::table('mahasiswas')
            ->join('prodis', 'mahasiswas.prodi_id', '=', 'prodis.kode_prodi')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($query) use ($like) {
                    $query
                        ->where('mahasiswas.nim', 'like', $like)
                        ->orWhere('mahasiswas.nama', 'like', $like)
                        ->orWhere('mahasiswas.tempat_lahir', 'like', $like)
                        ->orWhere('mahasiswas.th_masuk', 'like', $like)
                        ->orWhere('prodis.nama_prodi', 'like', $like)
                        ->orWhere('prodis.singkatan', 'like', $like);
                });
            })
            ->orderBy('mahasiswas.nim')
            ->get();
        return view('admin.mahasiswa', ['mhs' => $mahasiswa, 'cekRole' => $cekRole]);
    }
    //         return redirect('/mhs/show');
    //     } else {
    //         return redirect('/pinjam');
    //     }
    //     $mahasiswa = DB::table('mahasiswas')
    //         ->leftJoin('prodis', 'mahasiswas.prodi_id', '=', 'prodis.kode_prodi')
    //         ->select('mahasiswas.*', 'prodis.nama_prodi')
    //         ->get();

    //     return view('mahasiswa.index_mhs', ['mhs' => $mahasiswa]);
    // }

    // tambah method 
    public function tambah()
    {
        $prodi = DB::table('prodis')->orderBy('nama_prodi')->get();
        return view('mahasiswa.tambah_mhs', ['prodi' => $prodi]);
    }
    // simpan method
    public function simpan(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswas,nim',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            'prodi_id' => 'required',
            'th_masuk' => 'required|digits:4',
            'password' => 'required|min:4',
            'role' => 'required',
        ]);
        
        DB::table('mahasiswas')->insert([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'prodi_id' => $request->prodi_id,
            'th_masuk' => $request->th_masuk,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        return redirect('/mhs/show');
    }

// edit method
    public function edit($id)
    {
        $mahasiswa = DB::table('mahasiswas')->where('nim', $id)->first();
        $prodi = DB::table('prodis')->orderBy('nama_prodi')->get();
        return view('mahasiswa.edit_mhs', ['mhs' => $mahasiswa, 'prodi' => $prodi]);
    }
// update method
    public function update(Request $request, $id)
    {
        DB::table('mahasiswas')->where('nim', $id)->update([
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'prodi_id' => $request->prodi_id,
            'th_masuk' => $request->th_masuk,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        // return redirect()->back()->with('success', 'Data berhasil diperbarui!');
        return redirect('/mhs/show')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }
// hapus method
    public function hapus($id)
    {
        DB::table('mahasiswas')->where('nim', $id)->delete();
        return redirect('/mhs/show')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

// login

// untuk validasi login harus diisi
    public function login(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'password' => 'required',
        ]);
// auth untuk mahasiswa 
        if (Auth::guard('mahasiswas')->attempt([
            'nim' => $request ->nim,
            'password' => $request -> password,
        ])){
            return redirect()->intended('/mhs/show');
        }
        return back()->with('error', 'Nim atau Password salah');
    }

    // view login
    public function viewlogin()
    {
        return view('login.index_login');
    }
    public function logout(Request $request)
    {
        Auth::guard('mahasiswas')->logout();
        return redirect('/login');
    }
}