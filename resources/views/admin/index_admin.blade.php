@extends('layout.master')
@section('judul', 'Form admin')
@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<h2>Daftar Administrator</h2>
<a href="/admin/baru" class="btn btn-primary mb-3">Tambah Administrator</a>
<table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
  <thead>
    <tr role="row">
      <th>No</th>
      <th>ID</th>
      <th>Nama</th>
      <th>Email</th>
      <th>No Telp</th>
      <th>Alamat</th>
      <th>Role</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @forelse ($admins as $admin)
      <tr>
        <td>{{ $no++ }}</td>
        <td>{{ $admin->id }}</td>
        <td>{{ $admin->nama }}</td>
        <td>{{ $admin->email }}</td>
        <td>{{ $admin->no_telp }}</td>
        <td>{{ $admin->alamat }}</td>
        <td>{{ $admin->role }}</td>
        <td>
          <a href="/admin/edit/{{ $admin->id }}" class="btn btn-sm btn-warning">Edit</a>
          <a href="/admin/hapus/{{ $admin->id }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus admin {{ $admin->nama }}?')">Hapus</a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center">Belum ada data admin.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection