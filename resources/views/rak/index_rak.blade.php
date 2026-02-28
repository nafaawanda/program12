@extends('layout.master')
@section('judul', 'Rak Buku')
@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
@php
  $userRole = null;
  if(auth('admin')->check()) {
    $userRole = 'admin';
  } elseif(auth('staff')->check()) {
    $userRole = 'staff';
  } elseif(auth('mahasiswas')->check()) {
    $userRole = auth('mahasiswas')->user()->role;
  }
@endphp

@if ($userRole === 'admin')
  <a href="/rak/baru" class="btn btn-primary mb-3">Tambah Data Rak</a>
@endif

<table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
  <thead>
    <tr role="row">
      <th class="sorting sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 60px;">No</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 120px;">Kode Rak</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 200px;">Nama Rak</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 200px;">Lokasi</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 200px;">Keterangan</th>
      @if ($userRole === 'admin')
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" style="width: 120px;">Aksi</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @forelse ($rak as $r)
      <tr>
        <td>{{ $no++ }}</td>
        <td>{{ $r->kode_rak }}</td>
        <td>{{ $r->nama_rak }}</td>
        <td>{{ $r->lokasi }}</td>
        <td>{{ $r->keterangan }}</td>
        @if ($userRole === 'admin')
          <td>
            <a href="/rak/edit/{{ $r->kode_rak }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="/rak/hapus/{{ $r->kode_rak }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus rak {{ $r->nama_rak }}?')">Hapus</a>
          </td>
        @endif
      </tr>
    @empty
      <tr>
        <td colspan="{{ $userRole === 'admin' ? '6' : '5' }}" class="text-center">Belum ada data rak buku.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection
