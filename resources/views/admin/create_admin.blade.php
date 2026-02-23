@extends('layout.master')
@section('judul', 'Tambah Administrator')
@section('content')
<div class="container">
  <h2>Tambah Administrator</h2>
  <form action="/admin/simpan" method="POST">
    @csrf
    <div class="form-group">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
      <label>No Telp</label>
      <input type="text" name="no_telp" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Alamat</label>
      <input type="text" name="alamat" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Role</label>
      <input type="text" name="role" class="form-control" value="admin" readonly disabled>
      <input type="hidden" name="role" value="admin">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="/admin/show" class="btn btn-secondary">Batal</a>
  </form>
</div>
@endsection
