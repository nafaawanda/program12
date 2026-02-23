<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // Check if user is admin
    private function isAdmin()
    {
        $user = auth('admin')->user();
        return $user && $user->role === 'admin';
    }

    // Tampilkan daftar staff (Bisa diakses admin dan staff - read only)
    public function index(Request $request)
    {
        // Semua user yang login bisa melihat data staff
        $staffs = Staff::all();
        return view('staff.index', compact('staffs'));
    }

    // Form tambah staff
    public function create()
    {
        // Hanya admin yang bisa CRUD staff
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa menambah data staff');
        }
        return view('staff.create');
    }

    // Simpan staff baru
    public function store(Request $request)
    {
        // Hanya admin yang bisa CRUD staff
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa menambah data staff');
        }
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'email' => 'required|email|unique:staff,email',
            'password' => 'required|min:6',
        ]);
        Staff::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return redirect()->route('staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }

    // Form edit staff
    public function edit($id)
    {
        // Hanya admin yang bisa CRUD staff
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa mengedit data staff');
        }
        $staff = Staff::findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    // Update staff
    public function update(Request $request, $id)
    {
        // Hanya admin yang bisa CRUD staff
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa mengupdate data staff');
        }
        $staff = Staff::findOrFail($id);
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'email' => 'required|email|unique:staff,email,' . $staff->id_staff . ',id_staff',
        ]);
        $staff->nama = $request->nama;
        $staff->alamat = $request->alamat;
        $staff->no_telp = $request->no_telp;
        $staff->email = $request->email;
        if ($request->password) {
            $staff->password = bcrypt($request->password);
        }
        $staff->save();
        return redirect()->route('staff.index')->with('success', 'Staff berhasil diupdate.');
    }

    // Hapus staff
    public function destroy($id)
    {
        // Hanya admin yang bisa CRUD staff
        if (!$this->isAdmin()) {
            abort(403, 'Hanya admin yang bisa menghapus data staff');
        }
        
        // Cek apakah staff pernah melakukan peminjaman
        $peminjaman = DB::table('pinjams')
            ->where('nim', $id)
            ->where('borrower_type', 'staff')
            ->exists();
        
        if ($peminjaman) {
            return redirect()->route('staff.index')->with('error', 'Staff tidak bisa dihapus karena pernah melakukan transaksi peminjaman.');
        }
        
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}
