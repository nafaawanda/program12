
@extends('layout.master')
@section('judul', 'Form Prodi')
@section('content')
@php
    $authMhs = auth('mahasiswas');
    $authAdmin = auth('admin');
    if ($authAdmin->check()) {
        $currentRole = 'admin';
    } elseif ($authMhs->check()) {
        $currentRole = $authMhs->user()->role ?? null;
    } else {
        $currentRole = null;
    }
@endphp

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if ($currentRole === 'admin')
  <a href="/prd/baru" class="btn btn-primary mb-3">Tambah Data Prodi</a>
@endif

<table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
  <thead>
    <tr role="row">
      <th class="sorting sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="No: activate to sort column descending" style="width: 80px;">No</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Kode Prodi: activate to sort column ascending" style="width: 120px;">Kode Prodi</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Nama Prodi: activate to sort column ascending" style="width: 220px;">Nama Prodi</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Singkatan: activate to sort column ascending" style="width: 120px;">Singkatan</th>
      @if ($currentRole === 'admin')
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Aksi: activate to sort column ascending" style="width: 120px;">Aksi</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @forelse ($prd as $p)
      <tr>
        <td>{{ $no++ }}</td>
        <td>{{ $p->kode_prodi }}</td>
        <td>{{ $p->nama_prodi }}</td>
        <td>{{ $p->singkatan }}</td>
        @if ($currentRole === 'admin')
          <td>
            <a href="/prd/edit/{{ $p->kode_prodi }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="/prd/hapus/{{ $p->kode_prodi }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $p->nama_prodi }}?')">Hapus</a>
          </td>
        @endif
      </tr>
    @empty
      <tr>
        <td colspan="{{ $currentRole === 'admin' ? '5' : '4' }}" class="text-center">Belum ada data Prodi.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection