<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    // method index
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $kategori = $request->query('kategori');
        $kode_rak = $request->query('kode_rak');
        $penulis = $request->query('penulis');
        $penerbit = $request->query('penerbit');

        $buku = DB::table('bukus')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($query) use ($like) {
                    $query
                        ->where('kode_buku', 'like', $like)
                        ->orWhere('nama_buku', 'like', $like)
                        ->orWhere('nama_penulis', 'like', $like)
                        ->orWhere('jenis_buku', 'like', $like)
                        ->orWhere('penerbit', 'like', $like)
                        ->orWhere('th_terbit', 'like', $like)
                        ->orWhere('kode_rak', 'like', $like);
                });
            })
            ->when($kategori, function ($query) use ($kategori) {
                $query->where('jenis_buku', $kategori);
            })
            ->when($kode_rak, function ($query) use ($kode_rak) {
                $query->where('kode_rak', $kode_rak);
            })
            ->when($penulis, function ($query) use ($penulis) {
                $query->where('nama_penulis', $penulis);
            })
            ->when($penerbit, function ($query) use ($penerbit) {
                $query->where('penerbit', $penerbit);
            })
            ->orderBy('kode_buku')
            ->get();

        $kategoris = DB::table('bukus')->select('jenis_buku')->distinct()->pluck('jenis_buku');
        $kode_raks = DB::table('bukus')->select('kode_rak')->distinct()->pluck('kode_rak');
        $penuliss = DB::table('bukus')->select('nama_penulis')->distinct()->pluck('nama_penulis');
        $penerbits = DB::table('bukus')->select('penerbit')->distinct()->pluck('penerbit');

        return view('buku.index_bk', [
            'bk' => $buku,
            'kategoris' => $kategoris,
            'kode_raks' => $kode_raks,
            'penuliss' => $penuliss,
            'penerbits' => $penerbits
        ]);
    }
    
    // tambah method
    public function tambah()
    {
        return view('buku.tambah_bk');
    }
    
    // simpan method
    public function simpan(Request $request)
    {
        $request->validate([
            'kode_buku' => 'required|unique:bukus,kode_buku',
            'nama_buku' => 'required',
            'penerbit' => 'required',
            'th_terbit' => 'required|digits:4',
            'stock' => 'required'
        ]);
        
        DB::table('bukus')->insert([
            'kode_buku' => $request->kode_buku,
            'nama_buku' => $request->nama_buku,
            'nama_penulis' => $request->nama_penulis,
            'jenis_buku' => $request->jenis_buku,
            'penerbit' => $request->penerbit,
            'th_terbit' => $request->th_terbit,
            'stock' => $request->stock,
            'kode_rak' => $request->kode_rak
        ]);
        
        return redirect('/bk/show')->with('success', 'Data buku berhasil disimpan.');
    }

    // edit method
    public function edit($id)
    {
        $buku = DB::table('bukus')->where('kode_buku', $id)->first();
        return view('buku.edit_bk', ['bk' => $buku]);
    }
    
    // update method
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_buku' => 'required',
            'penerbit' => 'required',
            'th_terbit' => 'required|digits:4',
            'stock' => 'required'
        ]);
        
        DB::table('bukus')->where('kode_buku', $id)->update([
            'nama_buku' => $request->nama_buku,
            'nama_penulis' => $request->nama_penulis,
            'jenis_buku' => $request->jenis_buku,
            'penerbit' => $request->penerbit,
            'th_terbit' => $request->th_terbit,
            'stock' => $request->stock,
            'kode_rak' => $request->kode_rak
        ]);
        
        return redirect('/bk/show')->with('success', 'Data buku berhasil diperbarui!');
    }
    
    // hapus method
    public function hapus($id)
    {
        DB::table('bukus')->where('kode_buku', $id)->delete();
        return redirect('/bk/show')->with('success', 'Data buku berhasil dihapus.');
    }

    // Search suggestion (autocomplete) untuk topbar search
    public function searchSuggestion(Request $request)
    {
        $term = trim($request->query('term', ''));
        if ($term === '') {
            return response()->json([]);
        }

        $like = '%' . $term . '%';
        $results = DB::table('bukus')
            ->select('kode_buku', 'nama_buku', 'nama_penulis', 'jenis_buku')
            ->where('nama_buku', 'like', $like)
            ->orWhere('nama_penulis', 'like', $like)
            ->orWhere('kode_buku', 'like', $like)
            ->orderBy('nama_buku')
            ->get();

        return response()->json($results);
    }
}
