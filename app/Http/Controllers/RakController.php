<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RakController extends Controller
{
    // method index
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rak = DB::table('raks')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($query) use ($like) {
                    $query
                        ->where('kode_rak', 'like', $like)
                        ->orWhere('nama_rak', 'like', $like)
                        ->orWhere('lokasi', 'like', $like);
                });
            })
            ->orderBy('kode_rak')
            ->get();

        return view('rak.index_rak', ['rak' => $rak]);
    }

    // tambah method
    public function tambah()
    {
        return view('rak.tambah_rak');
    }

    // simpan method
    public function simpan(Request $request)
    {
        $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak',
            'nama_rak' => 'required',
        ]);

        DB::table('raks')->insert([
            'kode_rak'    => $request->kode_rak,
            'nama_rak'    => $request->nama_rak,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect('/rak/show')->with('success', 'Data rak berhasil disimpan.');
    }

    // edit method
    public function edit($id)
    {
        $rak = DB::table('raks')->where('kode_rak', $id)->first();
        return view('rak.edit_rak', ['rak' => $rak]);
    }

    // update method
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_rak' => 'required',
        ]);

        DB::table('raks')->where('kode_rak', $id)->update([
            'nama_rak'    => $request->nama_rak,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan,
            'updated_at'  => now(),
        ]);

        return redirect('/rak/show')->with('success', 'Data rak berhasil diperbarui!');
    }

    // hapus method
    public function hapus($id)
    {
        DB::table('raks')->where('kode_rak', $id)->delete();
        return redirect('/rak/show')->with('success', 'Data rak berhasil dihapus.');
    }
}
