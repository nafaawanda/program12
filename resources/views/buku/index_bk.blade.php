@extends('layout.master')
@section('judul', 'Form Buku')
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
  <a href="/bk/baru" class="btn btn-primary mb-3">Tambah Data Buku</a>
@elseif ($userRole === 'staff')
  <a href="/staff/bk/baru" class="btn btn-primary mb-3">Tambah Data Buku</a>
@endif
<form method="GET" action="">
  <div class="row mb-3">
    <div class="col">
      <select name="kategori" class="form-control" onchange="this.form.submit()">
        <option value="">-- Pilih Kategori --</option>
        @foreach ($kategoris ?? [] as $kategori)
          <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
        @endforeach
      </select>
    </div>
    <div class="col">
      <select name="kode_rak" class="form-control" onchange="this.form.submit()">
        <option value="">-- Pilih Kode Rak --</option>
        @foreach ($kode_raks ?? [] as $rak)
          <option value="{{ $rak }}" {{ request('kode_rak') == $rak ? 'selected' : '' }}>{{ $rak }}</option>
        @endforeach
      </select>
    </div>
    <div class="col">
      <select name="penulis" class="form-control" onchange="this.form.submit()">
        <option value="">-- Pilih Penulis --</option>
        @foreach ($penuliss ?? [] as $penulis)
          <option value="{{ $penulis }}" {{ request('penulis') == $penulis ? 'selected' : '' }}>{{ $penulis }}</option>
        @endforeach
      </select>
    </div>
    <div class="col">
      <select name="penerbit" class="form-control" onchange="this.form.submit()">
        <option value="">-- Pilih Penerbit --</option>
        @foreach ($penerbits ?? [] as $penerbit)
          <option value="{{ $penerbit }}" {{ request('penerbit') == $penerbit ? 'selected' : '' }}>{{ $penerbit }}</option>
        @endforeach
      </select>
    </div>
  </div>
</form>
<table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
  <thead>
    <tr role="row">
      <th class="sorting sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="No: activate to sort column descending" style="width: 400px;">No</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Kode Buku: activate to sort column ascending" style="width: 299px;">kode_buku</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Nama Buku: activate to sort column ascending" style="width: 299px;">nama_buku</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Nama Penulis: activate to sort column ascending" style="width: 299px;">nama_penulis</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Jenis Buku: activate to sort column ascending" style="width: 299px;">jenis_buku</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Penerbit: activate to sort column ascending" style="width: 160px;">penerbit</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Tahun Terbit: activate to sort column ascending" style="width: 260px;">th_terbit</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Stock: activate to sort column ascending" style="width: 260px;">stock</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Kode Rak: activate to sort column ascending" style="width: 260px;">kode_rak</th>
      @if ($userRole === 'admin' || $userRole === 'staff')
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Aksi: activate to sort column ascending" style="width: 260px;">aksi</th>
      @endif
    </tr>
  </thead>
  
  <tbody>
  @php $no = 1; @endphp
  @forelse ($bk as $b)
    <tr class="{{ $b->stock < 0 ? 'table-danger' : '' }}">
      <td>{{ $no++ }}</td>
      <td>{{ $b->kode_buku }}</td>
      <td>{{ $b->nama_buku }}</td>
      <td>{{ $b->nama_penulis }}</td>
      <td>{{ $b->jenis_buku }}</td>
      <td>{{ $b->penerbit }}</td>
      <td>{{ $b->th_terbit }}</td>
      <td>{{ $b->stock }}</td>
      <td>{{ $b->kode_rak }}</td>
      
      @if ($userRole === 'admin') 
        <td>
          <a href="/bk/edit/{{ $b->kode_buku }}" class="btn btn-sm btn-warning">Edit</a>
          <a href="/bk/hapus/{{ $b->kode_buku }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $b->nama_buku }}?')">Hapus</a>
        </td>
      @elseif ($userRole === 'staff')
        <td>
          <a href="/staff/bk/edit/{{ $b->kode_buku }}" class="btn btn-sm btn-warning">Edit</a>
          <!-- <a href="/staff/bk/hapus/{{ $b->kode_buku }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $b->nama_buku }}?')">Hapus</a> -->
        </td>
      @endif
    </tr>
  @empty
    <tr>
      <td colspan="{{ ($userRole === 'admin' || $userRole === 'staff') ? '10' : '9' }}" class="text-center">Belum ada data buku.</td>
    </tr>
  @endforelse
  </tbody>
</table>
@endsection