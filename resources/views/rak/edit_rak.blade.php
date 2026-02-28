@extends('layout.master')
@section('judul', 'Edit Rak Buku')
@section('content')
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="/rak/update/{{ $rak->kode_rak }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-group">
        <label>Kode Rak</label>
        <input type="text" name="kode_rak" class="form-control" value="{{ $rak->kode_rak }}" readonly>
    </div>
    <div class="form-group">
        <label>Nama Rak</label>
        <input type="text" name="nama_rak" placeholder="Masukkan Nama Rak" class="form-control" value="{{ $rak->nama_rak }}">
    </div>
    <div class="form-group">
        <label>Lokasi</label>
        <input type="text" name="lokasi" placeholder="Masukkan Lokasi Rak" class="form-control" value="{{ $rak->lokasi }}">
    </div>
    <div class="form-group">
        <label>Keterangan</label>
        <input type="text" name="keterangan" placeholder="Masukkan Keterangan" class="form-control" value="{{ $rak->keterangan }}">
    </div>
    <input type="submit" value="Simpan Data" class="btn btn-primary">
    <a href="/rak/show" class="btn btn-secondary">Batal</a>
</form>
@endsection
