@extends('layout.master')
@section('judul', 'Data Mahasiswa (Administrator)')
@section('content')
<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>NIM</th>
        <th>Nama</th>
        <th>Tempat Lahir</th>
        <th>Tgl Lahir</th>
        <th>Nama Prodi</th>
        <th>Th Masuk</th>
        <th>Role</th>
        <th>Email</th>
        <th>No Telp</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($mhs as $m)
      <tr>
        <td>{{ $loop->iteration }}</td>
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
          <a href="/mhs/edit/{{ $m->nim }}" class="btn btn-sm btn-warning">Edit</a>
          <a href="/mhs/hapus/{{ $m->nim }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data {{ $m->nama }}?')">Hapus</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
