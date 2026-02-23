@extends('layout.master')
@section('judul', 'Form mahasiswa')
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
  <a href="/mhs/baru" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
@elseif ($userRole === 'staff')
  <a href="/staff/mhs/baru" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
@endif
<div class="table-responsive">
<table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
  <thead>
    <tr role="row">
      <th class="sorting sorting_asc" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 400px;">No</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 299px;">nim</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 299px;">nama</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 160px;">tempat_lahir</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 285px;">tgl_lahir</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">nama_prodi</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">th_masuk</th>
      <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">role</th>
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">email</th>
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">no_telp</th>
        <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 160px;">Status</th>
        @if ($userRole === 'admin' || $userRole === 'staff')
          <th class="sorting" tabindex="0" aria-controls="dataTable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 260px;">aksi</th>
        @endif
      
    </tr>
  </thead>
  
  <tbody>
  @php $no = 1; @endphp
  @forelse ($mhs as $m)
    <tr>
      <td>{{ $no++ }}</td>
      <td>{{ $m->nim }}</td>
      <td>{{ $m->nama }}</td>
      <td>{{ $m->tempat_lahir }}</td>
      <td>{{ $m->tgl_lahir }}</td>
      <td>{{ $m->nama_prodi ?? $m->prodi_id }}</td>
      <td>{{ $m->th_masuk }}</td>
      <td>{{ $m->role }}</td>
      <td>{{ $m->email }}</td>
      <td>{{ $m->no_telp }}</td>
      <td>
        @if($userRole === 'admin')
          <form action="/mhs/status/{{ $m->nim }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')
            @if($m->status)
              <button type="submit" class="badge badge-success" style="border:none; background-color:#28a745; color:#fff; cursor:pointer;">Aktif</button>
            @else
              <button type="submit" class="badge badge-secondary" style="border:none; background-color:#6c757d; color:#fff; cursor:pointer;">Non-Aktif</button>
            @endif
          </form>
        @else
          @if($m->status)
            <span class="badge badge-success" style="background-color:#28a745; color:#fff;">Aktif</span>
          @else
            <span class="badge badge-secondary" style="background-color:#6c757d; color:#fff;">Non-Aktif</span>
          @endif
        @endif
      </td>
      @if ($userRole === 'admin')
        <td>
          <a href="/mhs/edit/{{ $m->nim }}" class="btn btn-sm btn-warning">Edit</a>
          <a href="/mhs/hapus/{{ $m->nim }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $m->nama }}?')">Hapus</a>
        </td>
      @elseif ($userRole === 'staff')
        <td>
          <a href="/staff/mhs/edit/{{ $m->nim }}" class="btn btn-sm btn-warning">Edit</a>
          <!-- <a href="/staff/mhs/hapus/{{ $m->nim }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $m->nama }}?')">Hapus</a> -->
        </td>
      @endif
    </tr>
  @empty
    <tr>
      <td colspan="9" class="text-center">Belum ada data mahasiswa.</td>
    </tr>
  @endforelse
  </tbody>
</table>
</div>
@endsection