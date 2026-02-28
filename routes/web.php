<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobaController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\PinjamController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RakController;

// Health check route for Render.com
Route::get('/ping', function () {
    return response()->json(['status' => 'ok'], 200);
});




// Route group untuk staff dengan prefix 'staff'
Route::prefix('staff')->middleware(['AuthMhs:staff,admin'])->group(function () {
    Route::post('/pinjam/simpan', [App\Http\Controllers\PinjamController::class, 'simpan'])->name('staff.pinjam.simpan');
    Route::get('/autocomplete-buku', [App\Http\Controllers\PinjamController::class, 'autocompleteBuku'])->name('staff.autocomplete.buku');
    Route::get('/autocomplete-mahasiswa', [App\Http\Controllers\PinjamController::class, 'autocompleteMahasiswa'])->name('staff.autocomplete.mahasiswa');
    Route::get('/autocomplete-peminjam', [App\Http\Controllers\PinjamController::class, 'autocompletePeminjam'])->name('staff.autocomplete.peminjam');
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');
    // Staff bisa LIHAT data staff (read-only)
    Route::get('/show', [App\Http\Controllers\StaffController::class, 'index'])->name('staff.index');
    Route::get('/mahasiswa', [App\Http\Controllers\CobaController::class, 'index'])->name('staff.mahasiswa');
    // CRUD Mahasiswa untuk staff
    Route::get('/mhs/baru', [App\Http\Controllers\CobaController::class, 'tambah'])->name('staff.mhs.baru');
    Route::post('/mhs/simpan', [App\Http\Controllers\CobaController::class, 'simpan'])->name('staff.mhs.simpan');
    Route::get('/mhs/edit/{id}', [App\Http\Controllers\CobaController::class, 'edit'])->name('staff.mhs.edit');
    Route::post('/mhs/update/{id}', [App\Http\Controllers\CobaController::class, 'update'])->name('staff.mhs.update');
    Route::get('/mhs/hapus/{id}', [App\Http\Controllers\CobaController::class, 'hapus'])->name('staff.mhs.hapus');
    Route::get('/prd/show', [App\Http\Controllers\ProdiController::class, 'index'])->name('staff.prd.index');
    Route::get('/bk/show', [App\Http\Controllers\BukuController::class, 'index'])->name('staff.bk.index');
    // CRUD Buku untuk staff
    Route::get('/bk/baru', [App\Http\Controllers\BukuController::class, 'tambah'])->name('staff.bk.baru');
    Route::post('/bk/simpan', [App\Http\Controllers\BukuController::class, 'simpan'])->name('staff.bk.simpan');
    Route::get('/bk/edit/{id}', [App\Http\Controllers\BukuController::class, 'edit'])->name('staff.bk.edit');
    Route::post('/bk/update/{id}', [App\Http\Controllers\BukuController::class, 'update'])->name('staff.bk.update');
    Route::get('/bk/hapus/{id}', [App\Http\Controllers\BukuController::class, 'hapus'])->name('staff.bk.hapus');
    Route::get('/pinjam', [App\Http\Controllers\PinjamController::class, 'index'])->name('staff.pinjam.index');
    Route::get('/kembali', [App\Http\Controllers\PinjamController::class, 'daftarPengembalian'])->name('staff.kembali.index');
    Route::get('/laporan/pengembalian', [App\Http\Controllers\PinjamController::class, 'laporanPengembalian'])->name('staff.laporan.pengembalian');
    // Pengembalian: endpoint get-by-nim untuk staff (AJAX)
    Route::get('/kembali/get-by-nim', [App\Http\Controllers\PinjamController::class, 'getPeminjamanByNim'])->name('staff.kembali.getByNim');
    // Pengembalian: proses pengembalian untuk staff
    Route::post('/kembali/proses', [App\Http\Controllers\PinjamController::class, 'prosesPengembalian'])->name('staff.kembali.proses');
});

// Route untuk update otomatis status buku hilang (bisa dipanggil manual atau via cron)
Route::get('/pinjam/update-hilang-otomatis', [App\Http\Controllers\PinjamController::class, 'updateStatusBukuHilangOtomatis'])->middleware('auth:admin');


// Route untuk update status mahasiswa (hanya admin)
Route::patch('/mhs/status/{nim}', [App\Http\Controllers\CobaController::class, 'updateStatus'])->middleware('auth:admin');


// Universal login (admin & mahasiswa) satu form
Route::get('/login', function () {
    return view('login_universal');
})->name('login.form');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.universal');

// Alias route untuk backward compatibility
Route::get('/login-alias', function () {
    return view('login_universal');
})->name('login');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Autocomplete pencarian buku (publik, untuk topbar search)
Route::get('/search/buku', [App\Http\Controllers\BukuController::class, 'searchSuggestion'])->name('search.buku');

Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect('/admin/dashboard');
    } elseif (auth('staff')->check()) {
        return redirect('/staff/dashboard');
    } elseif (auth('mahasiswas')->check()) {
        return redirect('/mhs');
    }
    return view('contoh.home');
});
Route::get('/hello', function () {
    return 'Hello World';
});

/* Route::get('/mahasiswa/{nama}', function ($nama) {
    return "Selamat Datang $nama di website kami";
});
 */
Route::get('/contoh', function () {
    return view('contoh.tampilan');
});

// Forgot Password Routes
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Admin Login
Route::get('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [App\Http\Controllers\AdminLoginController::class, 'logout'])->name('admin.logout');

// Mahasiswa Login
// Route login lama mahasiswa di-nonaktifkan, diganti dengan login mandiri
// Route::get('/mahasiswa/login', [App\Http\Controllers\MahasiswaLoginController::class, 'showLoginForm'])->name('mahasiswa.login');
Route::post('/mahasiswa/login', [App\Http\Controllers\MahasiswaLoginController::class, 'login'])->name('mahasiswa.login.post');
Route::post('/mahasiswa/logout', [App\Http\Controllers\MahasiswaLoginController::class, 'logout'])->name('mahasiswa.logout');
// Route::get('/mhs/show', function () {
//     return view('contoh.tampil_data', [
//         "mahasiswa01" => "Joko Lelono",
//         "mahasiswa02" => "Prabowo",
//         "mahasiswa03" => "Rafi"
//     ]);
// });



// Route dashboard mahasiswa (homepage after login)
Route::middleware('auth:mahasiswas')->group(function () {
    Route::get('/mhs', function () {
        $user = auth('mahasiswas')->user();
        return view('mahasiswa.dashboard', compact('user'));
    })->name('mahasiswa.dashboard');
});

// ============================================
// ROUTE LOGIN (TANPA MIDDLEWARE)
// ============================================
// Route login TIDAK boleh menggunakan middleware AuthMahasiswa
// karena user belum login saat mengakses halaman login
// Jika menggunakan middleware, akan terjadi redirect loop
// Route login lama di-nonaktifkan, semua login pakai universal
// Route::get('/login', [CobaController::class, 'viewlogin'])->name('viewlogin');
// Route::post('/login/simpan', [CobaController::class, 'login'])->name('login');
Route::get('/logout', [CobaController::class, 'logout'])->name('logout');

// ============================================
// ROUTE GROUP: AKSES UNTUK ADMIN DAN MAHASISWA
// ============================================
// Middleware 'AuthMhs:mhs,admin' artinya:
// - User harus sudah login (dicek oleh middleware)
// - Role user harus 'mhs' ATAU 'admin' (keduanya boleh akses)
//
// Route di dalam group ini bisa diakses oleh:
// ✓ Admin (role = 'admin')
// ✓ Mahasiswa (role = 'mhs')
// ✗ User yang belum login akan di-redirect ke halaman login
// ✗ User dengan role lain akan mendapat error 403
Route::middleware('AuthMhs:mhs,admin,staff')->group(function () {

    // ============================================
// ROUTE AKSES BERSAMA (ADMIN & MAHASISWA)
// ============================================
// Route di bawah ini bisa diakses oleh admin dan mahasiswa
// Admin dan mahasiswa memiliki akses yang sama untuk route ini


// Route untuk melihat data mahasiswa
// Mahasiswa: hanya bisa lihat data sendiri
    Route::get('/mhs/show', [CobaController::class, 'index'])->name('mhs.index');

    // Route untuk melihat data mahasiswa (admin)
    // Admin: bisa lihat semua data mahasiswa
    Route::get('/admin/mahasiswa', [App\Http\Controllers\Controller::class, 'index'])->name('admin.mahasiswa.index');

    // Route untuk melihat daftar buku
    // Baik admin maupun mahasiswa bisa melihat daftar buku
    Route::get('/bk/show', [\App\Http\Controllers\BukuController::class, 'index'])->name('bk.index');

    // Route untuk melihat daftar rak buku
    Route::get('/rak/show', [RakController::class, 'index'])->name('rak.index');

    // Route untuk melihat daftar prodi (bisa diakses admin & mahasiswa)
    Route::get('/prd/show', [\App\Http\Controllers\ProdiController::class, 'index'])->name('prd.index');

    // Route untuk melihat riwayat peminjaman
    // Admin: bisa lihat semua riwayat (jika diimplementasikan)
    // Mahasiswa: hanya bisa lihat riwayat sendiri
    Route::get('/pinjam/riwayat', [\App\Http\Controllers\PinjamController::class, 'riwayat'])->name('pinjam.riwayat');

    // ============================================
    // ROUTE CRUD MAHASISWA (HANYA ADMIN)
    // ============================================
    Route::middleware('AuthMhs:admin')->group(function () {
        Route::get('/mhs/baru', [CobaController::class, 'tambah']);
        Route::post('/mhs/simpan', [CobaController::class, 'simpan']);
        Route::get('/mhs/edit/{id}', [CobaController::class, 'edit']);
        Route::post('/mhs/update/{id}', [CobaController::class, 'update']);
        Route::get('/mhs/hapus/{id}', [CobaController::class, 'hapus']);
    });

    // ============================================
    // ROUTE CRUD BUKU (HANYA ADMIN)
    // ============================================
    Route::get('/bk/baru', [\App\Http\Controllers\BukuController::class, 'tambah']);
    Route::post('/bk/simpan', [\App\Http\Controllers\BukuController::class, 'simpan']);
    Route::get('/bk/edit/{id}', [\App\Http\Controllers\BukuController::class, 'edit']);
    Route::post('/bk/update/{id}', [\App\Http\Controllers\BukuController::class, 'update']);
    Route::get('/bk/hapus/{id}', [\App\Http\Controllers\BukuController::class, 'hapus']);

    // ============================================
    // ROUTE CRUD RAK BUKU (HANYA ADMIN)
    // ============================================
    Route::middleware('AuthMhs:admin')->group(function () {
        Route::get('/rak/baru', [RakController::class, 'tambah'])->name('rak.baru');
        Route::post('/rak/simpan', [RakController::class, 'simpan'])->name('rak.simpan');
        Route::get('/rak/edit/{id}', [RakController::class, 'edit'])->name('rak.edit');
        Route::post('/rak/update/{id}', [RakController::class, 'update'])->name('rak.update');
        Route::get('/rak/hapus/{id}', [RakController::class, 'hapus'])->name('rak.hapus');
    });

    // ============================================
    // ROUTE CRUD PRODI (HANYA ADMIN)
    // ============================================
    Route::get('/prd/baru', [\App\Http\Controllers\ProdiController::class, 'tambah']);
    Route::post('/prd/simpan', [\App\Http\Controllers\ProdiController::class, 'simpan']);
    Route::get('/prd/edit/{id}', [\App\Http\Controllers\ProdiController::class, 'edit']);
    Route::post('/prd/update/{id}', [\App\Http\Controllers\ProdiController::class, 'update']);
    Route::get('/prd/hapus/{id}', [\App\Http\Controllers\ProdiController::class, 'hapus']);

    // ============================================
    // ROUTE CRUD PRODIEL (HANYA ADMIN)
    // ============================================
    Route::get('/prodi', [\App\Http\Controllers\ProdiController::class, 'index']);
    Route::get('/prodi/baru', [\App\Http\Controllers\ProdiController::class, 'tambah']);
    Route::post('/prodi/simpan', [\App\Http\Controllers\ProdiController::class, 'simpan']);
    Route::get('/prodi/edit/{id}', [\App\Http\Controllers\ProdiController::class, 'edit']);
    Route::post('/prodi/update/{id}', [\App\Http\Controllers\ProdiController::class, 'update']);
    Route::get('/prodi/hapus/{id}', [\App\Http\Controllers\ProdiController::class, 'hapus']);

    // ============================================
    // ROUTE PEMINJAMAN BUKU (HANYA ADMIN)
    // ============================================
    Route::get('/pinjam', [\App\Http\Controllers\PinjamController::class, 'index'])->name('pinjam.index');
    Route::get('/autocomplete-mahasiswa', [\App\Http\Controllers\PinjamController::class, 'autocompleteMahasiswa'])->name('autocomplete.mahasiswa');
    Route::get('/autocomplete-buku', [\App\Http\Controllers\PinjamController::class, 'autocompleteBuku'])->name('autocomplete.buku');
    Route::get('/autocomplete-peminjam', [\App\Http\Controllers\PinjamController::class, 'autocompletePeminjam'])->name('autocomplete.peminjam');
    Route::post('/pinjam/simpan', [\App\Http\Controllers\PinjamController::class, 'simpan'])->name('pinjam.simpan');

    // ============================================
    // ROUTE PENGEMBALIAN BUKU (HANYA ADMIN)
    // ============================================
    Route::get('/kembali', [\App\Http\Controllers\PinjamController::class, 'daftarPengembalian'])->name('kembali');
    Route::get('/kembali/get-by-nim', [\App\Http\Controllers\PinjamController::class, 'getPeminjamanByNim'])->name('kembali.getByNim');
    Route::post('/kembali/proses', [\App\Http\Controllers\PinjamController::class, 'prosesPengembalian'])->name('kembali.proses');

    // ============================================
    // ROUTE LAPORAN (HANYA ADMIN)
    // ============================================
    Route::get('/laporan/pengembalian', [\App\Http\Controllers\PinjamController::class, 'laporanPengembalian'])->name('laporan.pengembalian');
});

// Route profil untuk semua user yang login (admin, mhs, staff)
Route::get('/profil', function () {
    if (auth('admin')->check()) {
        $user = auth('admin')->user();
    } elseif (auth('staff')->check()) {
        $user = auth('staff')->user();
    } elseif (auth('mahasiswas')->check()) {
        $user = auth('mahasiswas')->user();
    } else {
        return redirect()->route('login.form');
    }
    return view('profil', compact('user'));
})->name('profil');

// Route settings untuk semua user yang login (admin, mhs, staff)
Route::middleware('auth:admin,staff,mahasiswas')->group(function () {
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/password', [App\Http\Controllers\SettingsController::class, 'changePassword'])->name('settings.password');
});

// ============================================
// ROUTE GROUP: AKSES ADMIN DAN STAFF
// ============================================

// Route group untuk admin (HANYA ADMIN)
Route::middleware(['AuthMhs:admin'])->group(function () {
    // Route dashboard admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    // Route lama admin tetap ada jika dibutuhkan
    Route::get('/admin/show', function () {
        $admins = \App\Models\Admin::all();
        return view('admin.index_admin', ['admins' => $admins]);
    });
    // CRUD Admin
    Route::get('/admin/baru', [App\Http\Controllers\AdminCrudController::class, 'create']);
    Route::post('/admin/simpan', [App\Http\Controllers\AdminCrudController::class, 'store']);
    Route::get('/admin/edit/{id}', [App\Http\Controllers\AdminCrudController::class, 'edit']);
    Route::post('/admin/update/{id}', [App\Http\Controllers\AdminCrudController::class, 'update']);
    Route::get('/admin/hapus/{id}', [App\Http\Controllers\AdminCrudController::class, 'destroy']);
    // CRUD Staff (HANYA ADMIN)
    // Note: /staff/show sudah didefinisikan di grup staff prefix untuk admin & staff (read-only)
    // Route berikut hanya untuk operasi CRUD (create, store, edit, update, destroy) - admin saja
    Route::get('/staff/create', [App\Http\Controllers\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff/store', [App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/edit/{id}', [App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/update/{id}', [App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/destroy/{id}', [App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.destroy');
});

// Route group untuk staff (HANYA AKSES NON-CRUD STAFF)
// Note: /staff/dashboard sudah didefinisikan di grup prefix staff di atas

// Route untuk home mahasiswa (redirect ke dashboard mahasiswa)
Route::get('/mhs/home', function () {
    return redirect('/mhs');
});


// Route login mandiri mahasiswa
Route::get('/mahasiswa/showLoginForm', function () {
    return view('login.login_mahasiswa');
});

// Login Admin Mandiri (standalone)
// Route ini di-nonaktifkan karena sudah ada di baris 100
// Route::get('/admin/login', function () {
//     return view('login_admin');
// })->name('admin.login.mandiri');
