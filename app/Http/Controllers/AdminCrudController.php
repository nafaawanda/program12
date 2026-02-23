<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class AdminCrudController extends Controller
{
    // Show form tambah admin
    public function create()
    {
        // Batasi staff hanya bisa input, tidak bisa CRUD admin/staff
        $user = auth('admin')->user();
        if ($user && $user->role === 'staff') {
            // Staff hanya bisa input data, tidak bisa CRUD admin/staff
            return view('admin.create_admin');
        } elseif ($user && $user->role === 'admin') {
            return view('admin.create_admin');
        } else {
            abort(403, 'Akses ditolak');
        }
    }

    // Simpan admin baru
    public function store(Request $request)
    {
        $user = auth('admin')->user();
        if ($user && $user->role === 'staff') {
            // Staff hanya bisa input data, tidak bisa CRUD admin/staff
            $validated = $request->validate([
                'nama' => 'required',
                'email' => 'required|email|unique:admins,email',
                'no_telp' => 'required',
                'alamat' => 'required',
                'password' => 'required|min:6',
                'role' => 'required',
            ]);
            $validated['password'] = bcrypt($validated['password']);
            // Staff tidak bisa input admin/staff, hanya bisa input role lain (misal: mahasiswa)
            if ($validated['role'] === 'admin' || $validated['role'] === 'staff') {
                abort(403, 'Staff tidak bisa input admin/staff');
            }
            Admin::create($validated);
            return redirect('/staff/dashboard')->with('success', 'Data berhasil ditambahkan');
        } elseif ($user && $user->role === 'admin') {
            $validated = $request->validate([
                'nama' => 'required',
                'email' => 'required|email|unique:admins,email',
                'no_telp' => 'required',
                'alamat' => 'required',
                'password' => 'required|min:6',
                'role' => 'required',
            ]);
            $validated['password'] = bcrypt($validated['password']);
            Admin::create($validated);
            return redirect('/admin/show')->with('success', 'Admin berhasil ditambahkan');
        } else {
            abort(403, 'Akses ditolak');
        }
    }

    // Show form edit admin
    public function edit($id)
    {
        $user = auth('admin')->user();
        if ($user && $user->role === 'staff') {
            abort(403, 'Staff tidak bisa edit admin/staff');
        }
        $admin = Admin::findOrFail($id);
        return view('admin.edit_admin', compact('admin'));
    }

    // Update admin
    public function update(Request $request, $id)
    {
        $user = auth('admin')->user();
        if ($user && $user->role === 'staff') {
            abort(403, 'Staff tidak bisa update admin/staff');
        }
        $admin = Admin::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            'no_telp' => 'required',
            'alamat' => 'required',
            'role' => 'required',
        ]);
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        $admin->update($validated);
        return redirect('/admin/show')->with('success', 'Admin berhasil diupdate');
    }

    // Hapus admin
    public function destroy($id)
    {
        $user = auth('admin')->user();
        if ($user && $user->role === 'staff') {
            abort(403, 'Staff tidak bisa hapus admin/staff');
        }
        
        // Cek apakah admin pernah melakukan peminjaman
        $peminjaman = DB::table('pinjams')
            ->where('nim', $id)
            ->where('borrower_type', 'admin')
            ->exists();
        
        if ($peminjaman) {
            return redirect('/admin/show')->with('error', 'Admin tidak bisa dihapus karena pernah melakukan transaksi peminjaman.');
        }
        
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect('/admin/show')->with('success', 'Admin berhasil dihapus');
    }
}
