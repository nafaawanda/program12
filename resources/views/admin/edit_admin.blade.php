@extends('layout.master')
@section('judul', 'Edit Administrator')
@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<form action="/admin/update/{{ $admin->id }}" method="POST">
    @csrf
    <input type="text" name="nama" placeholder="Masukan Nama" class="form-control" value="{{ $admin->nama }}" required><br>
    <input type="email" name="email" placeholder="Masukan Email" class="form-control" value="{{ $admin->email }}" required><br>
    <input type="text" name="no_telp" placeholder="Masukan No Telp" class="form-control" value="{{ $admin->no_telp }}" required><br>
    <input type="text" name="alamat" placeholder="Masukan Alamat" class="form-control" value="{{ $admin->alamat }}" required><br>
    <input type="password" name="password" placeholder="Password (isi jika ingin ganti)" class="form-control"><br>
    <input type="text" name="role" class="form-control" value="admin" readonly disabled><br>
    <input type="hidden" name="role" value="admin">
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="/admin/show" class="btn btn-secondary">Batal</a>
</form>
@endsection