<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PinjamController extends Controller
{
    /**
     * Update otomatis status buku yang tidak dikembalikan selama 1 tahun menjadi 'hilang' (status = 2)
     */
    public function updateStatusBukuHilangOtomatis()
    {
        $setahunLalu = Carbon::now()->subYear();
        // Ambil detil pinjam yang status masih dipinjam (1) dan tgl_kembali 1 tahun lalu atau lebih
        $detilHilang = DB::table('detil_pinjams')
            ->join('pinjams', 'detil_pinjams.pinjam_id', '=', 'pinjams.id')
            ->where('detil_pinjams.status', 1)
            ->whereDate('pinjams.tgl_kembali', '<=', $setahunLalu)
            ->select('detil_pinjams.id')
            ->get();

        $ids = $detilHilang->pluck('id')->all();
        if (!empty($ids)) {
            DB::table('detil_pinjams')->whereIn('id', $ids)->update(['status' => 2]); // 2 = hilang
        }
        return count($ids);
    }
    /**
     * Cek koneksi database
     */
    public function checkDbConnection()
    {
        try {
            DB::connection()->getPdo();
            return 'Koneksi database berhasil!';
        } catch (\Exception $e) {
            return 'Tidak dapat terhubung ke database. Error: ' . $e->getMessage();
        }
    }
    /**
     * INDEX - Tampilkan list peminjaman
     */
    public function index(Request $request)
    {
        $pinjams = DB::table('pinjams')
            ->orderBy('tgl_pinjam', 'desc')
            ->get();

        $nims = $pinjams->pluck('nim')->unique()->map(fn($n)=> (string)$n)->filter()->values()->all();

        $mahasiswaMap = [];
        $mahasiswaRoleMap = [];
        if ($nims) {
            $mahasiswas = DB::table('mahasiswas')
                ->whereIn('nim',$nims)
                ->get();
            $mahasiswaMap = $mahasiswas->pluck('nama','nim')->toArray();
            $mahasiswaRoleMap = $mahasiswas->pluck('role','nim')->toArray();
        }

        $detilTotals = DB::table('detil_pinjams')
            ->select('pinjam_id',DB::raw('SUM(jml_buku) as total_buku'))
            ->groupBy('pinjam_id')
            ->pluck('total_buku','pinjam_id')
            ->toArray();

        // Ambil data detil untuk cek status telat
        $pinjamIds = $pinjams->pluck('id');
        $detilStatusMap = [];
        if ($pinjamIds->isNotEmpty()) {
            $detils = DB::table('detil_pinjams')
                ->whereIn('pinjam_id', $pinjamIds)
                ->where('status', 1) // Hanya yang masih dipinjam
                ->select('pinjam_id')
                ->get()
                ->groupBy('pinjam_id');
            
            foreach ($detils as $pinjamId => $detil) {
                $detilStatusMap[$pinjamId] = true; // Ada yang masih dipinjam
            }
        }

        $today = Carbon::today();
        $pinjam = $pinjams->map(function($p) use ($mahasiswaMap, $mahasiswaRoleMap, $detilTotals, $detilStatusMap, $today){
            $nim = (string)$p->nim;
            $p->mahasiswa_nama = $mahasiswaMap[$nim] ?? null;
            $p->mahasiswa_role = $mahasiswaRoleMap[$nim] ?? null;
            $p->total_buku = $detilTotals[$p->id] ?? 0;
            
            // Cek apakah telat
            $p->is_telat = false;
            if (isset($detilStatusMap[$p->id])) {
                // Ada buku yang masih dipinjam
                $tglKembali = Carbon::parse($p->tgl_kembali);
                $p->is_telat = $tglKembali->lt($today);
            }
            
            return $p;
        });

        return view('pinjam.index_pj', ['pj'=>$pinjam]);
    }


    /**
     * AUTOCOMPLETE BUKU
     */
    public function autocompleteBuku(Request $request)
    {
        $search = $request->term ?? '';

        $cari = DB::table('bukus')
            ->select('nama_buku','kode_buku','stock','nama_penulis','jenis_buku','kode_rak')
            ->when($search, fn($q)=> $q->where('nama_buku','like','%'.$search.'%'))
            ->orderBy('nama_buku','asc')
            ->limit(5)->get();

        $response = $cari->map(fn($b)=>[
            'value'=>$b->kode_buku,
            'label'=>$b->nama_buku,
            'stock'=>$b->stock,
            'nama_penulis'=>$b->nama_penulis ?? '',
            'jenis_buku'=>$b->jenis_buku ?? '',
            'kode_rak'=>$b->kode_rak ?? '',
        ]);

        return response()->json($response);
    }


    /**
     * AUTOCOMPLETE MAHASISWA
     */
    public function autocompleteMahasiswa(Request $request)
    {
        $search = $request->term ?? '';
        // Tidak ada filter role, tampilkan semua mahasiswa
        $cari = DB::table('mahasiswas')
            ->select('nama','nim')
            ->when($search, fn($q)=> $q->where('nama','like','%'.$search.'%'))
            ->orderBy('nama','asc')
            ->limit(5)->get();

        $response = $cari->map(fn($m)=>[
            "value"=>$m->nim,
            "label"=>$m->nama,
            "type"=>"mahasiswa"
        ]);

        return response()->json($response);
    }

    /**
     * AUTOCOMPLETE SEMUA PEMINJAM (Mahasiswa, Admin, Staff)
     */
    public function autocompletePeminjam(Request $request)
    {
        $search = $request->term ?? '';
        $results = [];
        
        // Cari di tabel mahasiswas
        $mahasiswas = DB::table('mahasiswas')
            ->select('nama', 'nim')
            ->when($search, fn($q)=> $q->where('nama','like','%'.$search.'%'))
            ->orderBy('nama','asc')
            ->limit(5)
            ->get()
            ->map(fn($m) => [
                'value' => $m->nim,
                'label' => $m->nama . ' (Mahasiswa)',
                'type' => 'mahasiswa',
                'id' => $m->nim
            ]);
        
        // Cari di tabel admins
        $admins = DB::table('admins')
            ->select('nama', 'id')
            ->when($search, fn($q)=> $q->where('nama','like','%'.$search.'%'))
            ->orderBy('nama','asc')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'value' => $a->id,
                'label' => $a->nama . ' (Admin)',
                'type' => 'admin',
                'id' => $a->id
            ]);
        
        // Cari di tabel staff
        $staffs = DB::table('staff')
            ->select('nama', 'id_staff')
            ->when($search, fn($q)=> $q->where('nama','like','%'.$search.'%'))
            ->orderBy('nama','asc')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'value' => $s->id_staff,
                'label' => $s->nama . ' (Staff)',
                'type' => 'staff',
                'id' => $s->id_staff
            ]);
        
        // Gabungkan semua hasil
        $results = $mahasiswas->concat($admins)->concat($staffs)->take(10)->values();

        return response()->json($results);
    }


    /**
     * Cek apakah mahasiswa memiliki buku yang terlambat dikembalikan
     * Method ini digunakan untuk validasi akses berdasarkan role
     * Hanya mahasiswa dengan role 'mhs' yang akan dicek tanggungan telatnya
     * 
     * @param string $nim NIM mahasiswa yang akan dicek
     * @return bool true jika ada buku telat, false jika tidak ada
     */

    private function cekTanggunganTelat($nim)
    {
        // Ambil tanggal hari ini untuk perbandingan
        $today = Carbon::today();
        // today() mengembalikan objek Carbon dengan tanggal hari ini tanpa waktu
        // Query untuk mencari buku yang:
        // 1. Dipinjam oleh mahasiswa dengan NIM tertentu
        // 2. Masih berstatus dipinjam (status = 1)
        // 3. Tanggal kembali sudah lewat dari hari ini (telat)
        $tanggunganTelat = DB::table('pinjams')
            ->join('detil_pinjams', 'pinjams.id', '=', 'detil_pinjams.pinjam_id')
            ->where('pinjams.nim', $nim)                    // Filter berdasarkan NIM
            ->where('detil_pinjams.status', 1)              // Status 1 = masih dipinjam (belum dikembalikan)
            ->whereDate('pinjams.tgl_kembali', '<', $today) // Tanggal kembali sudah lewat
            ->count(); // Hitung jumlah buku yang memenuhi kriteria
            
        // Kembalikan true jika ada buku telat (count > 0), false jika tidak ada
        return $tanggunganTelat > 0;
    }

    /**
     * SIMPAN DATA PINJAM
     * Method ini menangani proses penyimpanan data peminjaman buku
     * Terdapat beberapa tahap validasi sebelum data disimpan ke database
     */
    public function simpan(Request $request)
    {
        // ============================================
        // BAGIAN 0: CEK LOGIN STAFF ATAU ADMIN (PRIORITAS STAFF)
        // ============================================
        // Di sistem ini, "pegawai_id" dipakai untuk menyimpan ID staff/admin yang memproses peminjaman.
        // Jika staff login, gunakan staff. Jika tidak, baru admin. Jika tidak ada keduanya, fallback ke 1.
        $staff = Auth::guard('staff')->user();
        $admin = Auth::guard('admin')->user();
        
        // Initialize variables with proper defaults to avoid unassigned variable issues
        $pegawaiId = null;
        $pegawaiNama = null;
        $pegawaiRole = null;
        $pegawaiType = null;
        
        // Determine the employee type and set appropriate values
        if ($staff) {
            $pegawaiId = $staff->id_staff;
            $pegawaiNama = $staff->nama;
            $pegawaiRole = 'staff';
            $pegawaiType = 'staff';
        } elseif ($admin) {
            $pegawaiId = $admin->id;
            $pegawaiNama = $admin->nama;
            $pegawaiRole = 'admin';
            $pegawaiType = 'admin';
        }
        
        // If no authenticated user found, use default fallback values
        if ($pegawaiId === null) {
            $pegawaiId = '1';
            $pegawaiNama = 'System';
            $pegawaiRole = 'system';
            $pegawaiType = 'system';
        }
        Log::info('[PINJAM] Simpan peminjaman', [
            'pegawai_id' => $pegawaiId,
            'pegawai_nama' => $pegawaiNama,
            'pegawai_role' => $pegawaiRole,
            'user_agent' => $request->header('User-Agent'),
            'ip' => $request->ip(),
        ]);

        // ============================================
        // BAGIAN 1: VALIDASI NIM DAN IDENTITAS PEMINJAM
        // ============================================
        $nim = $request->nim;
        $borrowerType = $request->borrower_type ?? 'mahasiswa'; // Default ke mahasiswa
        
        // Validasi borrower_type harus valid
        if (!in_array($borrowerType, ['mahasiswa', 'admin', 'staff'])) {
            $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
            return redirect($redirectUrl)->with('error', 'Tipe peminjam tidak valid.');
        }
        
        // Jika borrower adalah staff atau admin, gunakan ID mereka sebagai identitas
        $pegawaiPinjamanId = null;
        $pegawaiPinjamanNama = null;
        $isPegawaiPinjaman = false;
        
        // Jika borrower_type staff atau admin,nim berisi ID staff/admin
        if ($borrowerType === 'staff') {
            $staffId = $nim; // nim field berisi staff ID
            if ($staffId) {
                $staffData = DB::table('staff')->where('id_staff', $staffId)->first();
                if ($staffData) {
                    $pegawaiPinjamanId = $staffData->id_staff;
                    $pegawaiPinjamanNama = $staffData->nama;
                    $isPegawaiPinjaman = true;
                } else {
                    $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                    return redirect($redirectUrl)->with('error', 'Staff dengan ID ' . $staffId . ' tidak ditemukan.');
                }
            } else {
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'ID Staff tidak boleh kosong.');
            }
        } elseif ($borrowerType === 'admin') {
            $adminId = $nim; // nim field berisi admin ID
            if ($adminId) {
                $adminData = DB::table('admins')->where('id', $adminId)->first();
                if ($adminData) {
                    $pegawaiPinjamanId = $adminData->id;
                    $pegawaiPinjamanNama = $adminData->nama;
                    $isPegawaiPinjaman = true;
                } else {
                    $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                    return redirect($redirectUrl)->with('error', 'Admin dengan ID ' . $adminId . ' tidak ditemukan.');
                }
            } else {
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'ID Admin tidak boleh kosong.');
            }
        } elseif ($borrowerType === 'mahasiswa') {
            // Validasi NIM tidak boleh kosong untuk mahasiswa
            if (!$nim) {
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'NIM tidak boleh kosong.');
            }
            
            // Cek apakah mahasiswa dengan NIM tersebut ada di database
            $mahasiswa = DB::table('mahasiswas')->where('nim', $nim)->first();
            
            // Jika mahasiswa tidak ditemukan, kembalikan error
            if (!$mahasiswa) {
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
            }
            
            // PENGECEKAN ROLE: Hanya mahasiswa dengan role 'mhs' yang dicek tanggungan telat
            if ($mahasiswa->role === 'mhs') {
                if ($this->cekTanggunganTelat($nim)) {
                    $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                    return redirect($redirectUrl)->with('error', 'Mahasiswa dengan NIM ' . $nim . ' tidak dapat meminjam buku karena masih memiliki buku yang terlambat dikembalikan. Silakan kembalikan buku yang terlambat terlebih dahulu.');
                }
            }
        }
        
        // Jika ini pinjaman oleh staff/admin, skip validasi mahasiswa
        if ($isPegawaiPinjaman) {
            Log::info('[PINJAM] Peminjaman oleh staff/admin', [
                'borrower_type' => $borrowerType,
                'borrower_id' => $pegawaiPinjamanId,
                'borrower_nama' => $pegawaiPinjamanNama,
            ]);
        } else {
            // Validasi mahasiswa untuk pinjaman reguler
            // Cek apakah mahasiswa dengan NIM tersebut ada di database
            $mahasiswa = DB::table('mahasiswas')->where('nim', $nim)->first();
            
            // Jika mahasiswa tidak ditemukan, kembalikan error
            if (!$mahasiswa) {
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
            }
            
            // PENGECEKAN ROLE: Hanya mahasiswa dengan role 'mhs' yang dicek tanggungan telat
            // Admin tidak perlu dicek tanggungan telat karena memiliki akses khusus
            // Jika mahasiswa yang akan meminjam memiliki role 'mhs' (mahasiswa biasa)
            if ($mahasiswa->role === 'mhs') {
                // Cek apakah mahasiswa masih memiliki buku yang terlambat dikembalikan
                // Method cekTanggunganTelat() akan mengembalikan true jika ada buku telat
                if ($this->cekTanggunganTelat($nim)) {
                    // Jika ada tanggungan telat, tolak peminjaman dan beri pesan error
                    $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                    return redirect($redirectUrl)->with('error', 'Mahasiswa dengan NIM ' . $nim . ' tidak dapat meminjam buku karena masih memiliki buku yang terlambat dikembalikan. Silakan kembalikan buku yang terlambat terlebih dahulu.');
                }
            }
            // Jika role adalah 'admin', skip pengecekan tanggungan telat (admin bisa pinjam meski ada tanggungan)
        }
        
        // ============================================
        // BAGIAN 3: VALIDASI DATA BUKU
        // ============================================
        // Ambil data buku dari request (berbentuk array karena bisa pinjam banyak buku)
        $kode_buku = $request->input('kode_buku', []);
        $jumlah_pinjam = $request->input('jumlah_pinjam', []);
        
        // Validasi: Pastikan ada buku yang dipinjam dan jumlah array sama
        if (empty($kode_buku) || empty($jumlah_pinjam) || count($kode_buku) !== count($jumlah_pinjam)) {
            $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
            return redirect($redirectUrl)->with('error', 'Data buku tidak boleh kosong atau tidak valid. Silakan tambahkan buku terlebih dahulu.');
        }
        
        // ============================================
        // BAGIAN 4: VALIDASI STOK BUKU
        // ============================================
        // Array untuk menyimpan semua error validasi stok
        $errors = [];
        
        // Loop untuk mengecek setiap buku yang akan dipinjam
        for($i=0; $i<count($kode_buku); $i++){
            // Validasi ekstra: pastikan key ada di kedua array
            if (!isset($kode_buku[$i]) || !isset($jumlah_pinjam[$i])) {
                $errors[] = 'Data buku tidak valid.';
                continue;
            }
            // Ambil data buku dari database berdasarkan kode_buku
            $buku = DB::table('bukus')->where('kode_buku', $kode_buku[$i])->first();
            // Validasi: Cek apakah buku ada di database
            if (!$buku) {
                $errors[] = 'Buku dengan kode ' . $kode_buku[$i] . ' tidak ditemukan.';
                continue;
            }
            // Ambil stok tersedia dari database buku
            $stockTersedia = $buku->stock;
            // Konversi jumlah peminjaman ke integer untuk perbandingan
            $jumlahRequest = (int)$jumlah_pinjam[$i];
            // VALIDASI 1: Cek apakah jumlah peminjaman lebih dari 0
            if ($jumlahRequest <= 0) {
                $errors[] = 'Jumlah peminjaman untuk buku ' . ($buku->nama_buku ?? $kode_buku[$i]) . ' harus lebih dari 0.';
            }
            // VALIDASI 2: Cek apakah jumlah peminjaman melebihi stok tersedia
            elseif ($jumlahRequest > $stockTersedia) {
                $errors[] = 'Jumlah peminjaman untuk buku "' . ($buku->nama_buku ?? $kode_buku[$i]) . '" (' . $jumlahRequest . ') melebihi stok yang tersedia (' . $stockTersedia . ').';
            }
        }
        
        // Jika ada error validasi stok, kembalikan semua error dan batalkan proses
        if (!empty($errors)) {
            $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
            return redirect($redirectUrl)->with('error', implode(' ', $errors));
        }
        
        // ============================================
        // BAGIAN 5: PENYIMPANAN DATA KE DATABASE
        // ============================================
        // Mulai transaksi database
        // beginTransaction() adalah fungsi untuk memulai transaksi database
        // Transaksi database adalah mekanisme untuk memastikan bahwa semua operasi database 
        // berjalan secara konsisten dan atomik (ACID: Atomicity, Consistency, Isolation, Durability)
        // Artinya: jika ada salah satu operasi database yang gagal, 
        // maka semua operasi database akan dibatalkan (rollback)
        DB::beginTransaction();
        
        // try-catch: Blok kode untuk menangani error
        // try: blok kode yang akan dijalankan jika transaksi database berhasil
        // catch: blok kode yang akan dijalankan jika terjadi error/exception
        try {
            // ============================================
            // SUB BAGIAN 5.1: SIMPAN DATA PEMINJAMAN UTAMA
            // ============================================
            // Simpan data peminjaman ke tabel 'pinjams'
            // Tabel ini menyimpan informasi utama peminjaman (NIM, tanggal, pegawai)
            $pinjam = new Pinjam;
            
            // Jika peminjam adalah staff atau admin, gunakan ID mereka sebagai nim
            if ($isPegawaiPinjaman) {
                $pinjam->nim = $pegawaiPinjamanId;  // ID staff/admin sebagai identitas peminjam
                $pinjam->borrower_type = $borrowerType;  // Tipe peminjam (staff/admin)
            } else {
                $pinjam->nim = $request->nim;  // NIM mahasiswa yang meminjam
                $pinjam->borrower_type = 'mahasiswa';  // Tipe peminjam (mahasiswa)
            }
            
            $pinjam->tgl_pinjam   = $request->tgl_pinjam;   // Tanggal peminjaman
            $pinjam->tgl_kembali  = $request->tgl_kembali;  // Tanggal pengembalian yang dijadwalkan
            $pinjam->pegawai_id   = $pegawaiId;              // ID admin/staff yang menangani peminjaman
            $pinjam->pegawai_type = $pegawaiType;            // Tipe pegawai (admin/staff)
            $pinjam->save(); // Simpan ke database

            // ============================================
            // SUB BAGIAN 5.2: SIMPAN DATA DETIL PEMINJAMAN
            // ============================================
            $detilBerhasil = 0;
            // Ambil kembali data buku dari request untuk disimpan ke detil
            $kode_buku  = $request->input('kode_buku',[]);      // Array kode buku yang dipinjam
            $jumlah_pinjam = $request->input('jumlah_pinjam',[]); // Array jumlah buku yang dipinjam
            $status     = $request->input('status',[]);           // Array status peminjaman (1 = masih dipinjam)

            // Loop untuk menyimpan setiap buku yang dipinjam ke tabel detil_pinjams
            for($i=0; $i<count($kode_buku); $i++){
                // Validasi ekstra: pastikan semua data array ada
                if (!isset($kode_buku[$i]) || !isset($jumlah_pinjam[$i]) || !isset($status[$i])) {
                    continue;
                }
                // Ambil kode_rak dari request
                $kode_rak = isset($request->kode_rak[$i]) ? $request->kode_rak[$i] : null;
                // Simpan detil peminjaman ke tabel 'detil_pinjams'
                DB::table('detil_pinjams')->insert([
                    'pinjam_id'=> $pinjam->id,
                    'kode_buku'=> $kode_buku[$i],
                    'jml_buku'=>  $jumlah_pinjam[$i],
                    'status'=>    $status[$i],
                    'kode_rak'=>  $kode_rak
                ]);
                // Kurangi stok buku di tabel 'bukus'
                DB::table('bukus')
                    ->where('kode_buku',$kode_buku[$i])
                    ->decrement('stock',$jumlah_pinjam[$i]);
                $detilBerhasil++;
            }
            // Jika tidak ada detil yang berhasil disimpan, rollback dan error
            if ($detilBerhasil === 0) {
                DB::rollBack();
                $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
                return redirect($redirectUrl)->with('error', 'Data buku tidak valid. Tidak ada buku yang berhasil dipinjam.');
            }
            
            // ============================================
            // SUB BAGIAN 5.4: COMMIT TRANSAKSI
            // ============================================
            // Commit transaksi jika semua operasi berhasil
            // commit() akan menyimpan semua perubahan ke database secara permanen
            // Setelah commit, data tidak bisa di-rollback lagi
            DB::commit();
            
        } catch (\Exception $e) {
            // ============================================
            // BAGIAN 6: PENANGANAN ERROR
            // ============================================
            // Rollback transaksi jika terjadi error
            // rollback() akan membatalkan semua perubahan yang dilakukan dalam transaksi
            // Data akan kembali ke keadaan sebelum beginTransaction()
            DB::rollback();
            
            // Kembalikan error message ke user
            $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
            return redirect($redirectUrl)->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        // ============================================
        // BAGIAN 7: SUKSES
        // ============================================
        // Jika semua proses berhasil, kembalikan pesan sukses
        $redirectUrl = $staff ? '/staff/pinjam' : '/pinjam';
        return redirect($redirectUrl)->with('success','Data peminjaman berhasil disimpan.');
    }


    /**
     * Menampilkan daftar peminjaman yang belum dikembalikan
     */
    public function daftarPengembalian()
    {
        $pinjams = collect(); // Kosongkan karena akan diisi via AJAX

        return view('kembali.index_bk', compact('pinjams'));
    }

    /**
     * Get data peminjaman berdasarkan NIM (untuk AJAX)
     */
    public function getPeminjamanByNim(Request $request)
    {
        $nim = $request->nim;

        if (!$nim) {
            return response()->json([
                'success' => false,
                'message' => 'NIM tidak boleh kosong'
            ], 400);
        }

        $pinjams = DB::table('pinjams')
            ->join('detil_pinjams', 'pinjams.id', '=', 'detil_pinjams.pinjam_id')
            ->join('bukus', 'detil_pinjams.kode_buku', '=', 'bukus.kode_buku')
            ->join('mahasiswas', 'pinjams.nim', '=', 'mahasiswas.nim')
            ->select('pinjams.*', 'bukus.nama_buku as judul_buku', 'mahasiswas.nama as nama_peminjam', 'detil_pinjams.id as detil_id', 'detil_pinjams.jml_buku', 'detil_pinjams.kode_rak')
            ->where('pinjams.nim', $nim)
            ->where('detil_pinjams.status', 1) // Hanya tampilkan yang belum dikembalikan
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pinjams
        ]);
    }


    /**
     * Memproses pengembalian buku
     */
    public function prosesPengembalian(Request $request)
    {
        // Validasi request
        $request->validate([
            'detil_id' => 'required|exists:detil_pinjams,id',
        ]);

        // Ambil detil_id dari request POST
        $detil_id = $request->detil_id;

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Dapatkan kode buku dan jumlah yang dipinjam sebelum update
            $detil = DB::table('detil_pinjams')
                ->where('id', $detil_id)
                ->first();

            if (!$detil) {
                throw new \Exception('Data detil peminjaman tidak ditemukan');
            }

            // Validasi: jika status sudah hilang, tolak pengembalian
            if ($detil->status == 2) {
                DB::rollBack();
                return $this->redirectPengembalian('error', 'Buku ini sudah berstatus HILANG dan tidak bisa dikembalikan.');
            }

            // Ambil data pinjam terkait untuk cek tanggal kembali
            $pinjam = DB::table('pinjams')->where('id', $detil->pinjam_id)->first();
            
            // Add null check for $pinjam to prevent errors
            if (!$pinjam) {
                DB::rollBack();
                return $this->redirectPengembalian('error', 'Data peminjaman tidak ditemukan.');
            }
            
            $today = \Carbon\Carbon::today();
            $tglKembali = \Carbon\Carbon::parse($pinjam->tgl_kembali);

            // Jika pengembalian lebih dari 1 tahun dari tgl_kembali, status otomatis jadi hilang (2)
            if ($tglKembali && $tglKembali->copy()->addYear()->lte($today)) {
                // Update status menjadi hilang
                DB::table('detil_pinjams')
                    ->where('id', $detil_id)
                    ->update([
                        'status' => 2 // 2 = hilang
                    ]);
                // Tidak menambah stok buku karena dianggap hilang
                DB::commit();
                return $this->redirectPengembalian('error', 'Buku dikembalikan lebih dari 1 tahun setelah tanggal kembali. Status otomatis menjadi HILANG. Stok buku tidak bertambah.');
            } else {
                // Update status peminjaman menjadi 0 (sudah dikembalikan)
                DB::table('detil_pinjams')
                    ->where('id', $detil_id)
                    ->update([
                        'status' => 0 // Sudah dikembalikan
                    ]);

                // Update stok buku (tambah kembali stok yang dipinjam)
                DB::table('bukus')
                    ->where('kode_buku', $detil->kode_buku)
                    ->increment('stock', $detil->jml_buku);

                // Commit transaksi
                DB::commit();

                return $this->redirectPengembalian('success', 'Buku berhasil dikembalikan. Stok buku telah diperbarui.');
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    // Helper: redirect ke halaman pengembalian sesuai role
    private function redirectPengembalian($type, $msg) {
        if (auth('staff')->check()) {
            return redirect()->route('staff.kembali.index')->with($type, $msg);
        } else {
            return redirect()->route('kembali')->with($type, $msg);
        }
    }
    
    /**
     * Menampilkan riwayat peminjaman untuk mahasiswa yang sedang login
     * Method ini menampilkan riwayat peminjaman berdasarkan role:
     * - Mahasiswa biasa (mhs): hanya melihat riwayat sendiri
     * - Admin: bisa melihat semua riwayat (jika diimplementasikan)
     */
    public function riwayat()
    {
        // ============================================
        // BAGIAN 1: VALIDASI AKSES (AUTHENTICATION)
        // ============================================
        // Ambil data user yang sedang login menggunakan guard 'mahasiswas'
        // Guard adalah mekanisme autentikasi untuk menentukan user yang login
        // Cek login admin
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            // Admin: akses semua riwayat
            $pinjams = DB::table('pinjams')
                ->orderBy('tgl_pinjam', 'desc')
                ->get();
            $mahasiswa = null;
            // Debug admin: cek data pinjams
            // dd(['admin'=>$admin, 'pinjams'=>$pinjams]);
        } else {
            // Cek login mahasiswa
            $mahasiswa = Auth::guard('mahasiswas')->user();
            if (!$mahasiswa) {
                return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
            }
            // Mahasiswa: akses riwayat sendiri
            $pinjams = DB::table('pinjams')
                ->where('nim', $mahasiswa->nim)
                ->orderBy('tgl_pinjam', 'desc')
                ->get();
            // Debug mahasiswa: cek data pinjams
            // dd(['mahasiswa'=>$mahasiswa, 'pinjams'=>$pinjams]);
        }

        $detilMap = [];
        if ($pinjams->isNotEmpty()) {
            $pinjamIds = $pinjams->pluck('id');
            $detilMap = DB::table('detil_pinjams')
                ->join('bukus', 'detil_pinjams.kode_buku', '=', 'bukus.kode_buku')
                ->select(
                    'detil_pinjams.pinjam_id',
                    'detil_pinjams.kode_buku',
                    'detil_pinjams.jml_buku',
                    'detil_pinjams.status',
                    'bukus.nama_buku as judul_buku'
                )
                ->whereIn('detil_pinjams.pinjam_id', $pinjamIds)
                ->get()
                ->groupBy('pinjam_id');
        }

        $today = Carbon::today();
        // Map data peminjaman untuk menampilkan riwayat peminjaman
        $riwayat = $pinjams->map(function ($pinjam) use ($detilMap, $today) {
            $pinjam->detil = $detilMap[$pinjam->id] ?? collect();
            // Cek apakah tanggal kembali sudah lewat
            $pinjam->is_telat = Carbon::parse($pinjam->tgl_kembali)->lt($today) && 
                                $pinjam->detil->where('status', 1)->isNotEmpty();
            return $pinjam;
        });

        return view('pinjam.riwayat_pj', [
            'riwayat' => $riwayat,
            'mahasiswa' => $mahasiswa,
        ]);
    }
    
    /**
     * Helper method untuk mendapatkan jumlah buku telat mahasiswa
     */
    public static function getBukuTelat($nim)
    {
        $today = Carbon::today();
        
        $bukuTelat = DB::table('pinjams')
            ->join('detil_pinjams', 'pinjams.id', '=', 'detil_pinjams.pinjam_id')
            ->join('bukus', 'detil_pinjams.kode_buku', '=', 'bukus.kode_buku')
            ->select(
                'pinjams.id',
                'pinjams.tgl_kembali',
                'bukus.nama_buku',
                'detil_pinjams.jml_buku'
            )
            ->where('pinjams.nim', $nim)
            ->where('detil_pinjams.status', 1) // Status 1 = masih dipinjam
            ->whereDate('pinjams.tgl_kembali', '<', $today)
            ->get();
            
        return $bukuTelat;
    }
    
    /**
     * Laporan Pengembalian untuk Admin
     */
    public function laporanPengembalian()
    {
        // Update otomatis status buku hilang sebelum ambil data laporan
        $this->updateStatusBukuHilangOtomatis();
        // Ambil semua data peminjaman dengan detail
        $laporan = DB::table('pinjams')
            ->leftJoin('mahasiswas', 'pinjams.nim', '=', 'mahasiswas.nim')
            ->leftJoin('admins as admin', 'pinjams.pegawai_id', '=', 'admin.id')
            ->leftJoin('staff as staff', 'pinjams.pegawai_id', '=', 'staff.id_staff')
            ->leftJoin('detil_pinjams', 'pinjams.id', '=', 'detil_pinjams.pinjam_id')
            ->leftJoin('bukus', 'detil_pinjams.kode_buku', '=', 'bukus.kode_buku')
            ->select(
                'pinjams.id as pinjam_id',
                'pinjams.nim',
                'pinjams.borrower_type',
                DB::raw('COALESCE(mahasiswas.nama, pinjams.nim) as nama_mahasiswa'),
                DB::raw('CASE 
                    WHEN pinjams.borrower_type IN ("admin", "staff") THEN 1 
                    ELSE COALESCE(mahasiswas.status, 0) 
                END as status_mahasiswa'),
                DB::raw('COALESCE(mahasiswas.role, pinjams.borrower_type) as role'),
                    // Ambil nama dan label pegawai_type secara eksplisit
                    'admin.nama as admin_nama',
                    'staff.nama as staff_nama',
                    'pinjams.pegawai_id as pegawai_id',
                    'pinjams.pegawai_type as pegawai_type',
                    'pinjams.tgl_pinjam',
                    'pinjams.tgl_kembali',
                    'bukus.nama_buku',
                    'bukus.kode_buku',
                    'detil_pinjams.jml_buku',
                    'detil_pinjams.kode_rak',
                    'detil_pinjams.status',
                    'detil_pinjams.id as detil_id'
                )
                ->orderBy('pinjams.nim', 'asc')
                ->orderBy('pinjams.tgl_pinjam', 'desc')
                ->get();

            // Proses agar nama_pegawai dan label_pegawai_type konsisten untuk data lama & baru
            $laporan = $laporan->map(function($item) {
                // Default
                $item->nama_pegawai = '-';
                $item->label_pegawai_type = null;
                if ($item->pegawai_type === 'admin') {
                    $item->nama_pegawai = $item->admin_nama;
                    $item->label_pegawai_type = 'Admin';
                } elseif ($item->pegawai_type === 'staff') {
                    $item->nama_pegawai = $item->staff_nama;
                    $item->label_pegawai_type = 'Staff';
                } else {
                    // Data lama: cek mana yang ada
                    if ($item->admin_nama) {
                        $item->nama_pegawai = $item->admin_nama;
                        $item->label_pegawai_type = 'Admin';
                    } elseif ($item->staff_nama) {
                        $item->nama_pegawai = $item->staff_nama;
                        $item->label_pegawai_type = 'Staff';
                    }
                }
                return $item;
            });

        $today = Carbon::today();
        
        // Tambahkan informasi status telat
        $laporan = $laporan->map(function($item) use ($today) {
            $tglKembali = Carbon::parse($item->tgl_kembali);
            $item->is_telat = $tglKembali->lt($today) && $item->status == 1;
            $item->status_text = $item->status == 1 ? 'Belum Dikembalikan' : 'Sudah Dikembalikan';
            return $item;
        });

        // Group by pinjam_id untuk statistik
        $statistik = [
            'total_peminjaman' => $laporan->pluck('pinjam_id')->unique()->count(),
            'total_belum_kembali' => $laporan->where('status', 1)->count(),
            'total_telat' => $laporan->where('is_telat', true)->count(),
            'total_sudah_kembali' => $laporan->where('status', 0)->count(),
        ];

        return view('pinjam.laporan_pengembalian', [
            'laporan' => $laporan,
            'statistik' => $statistik
        ]);
    }
}