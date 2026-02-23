@extends('layout.master')

@section('judul', 'Daftar Staff')

@php
$isAdmin = auth('admin')->check() && auth('admin')->user()->role === 'admin';
@endphp

@section('content')
<div class="container-fluid">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <h1 class="h3 mb-4 text-gray-800">Daftar Staff</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Staff</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            @if($isAdmin)
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffs as $i => $staff)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $staff->id_staff }}</td>
                            <td>{{ $staff->nama }}</td>
                            <td>{{ $staff->alamat }}</td>
                            <td>{{ $staff->no_telp }}</td>
                            <td>{{ $staff->email }}</td>
                            @if($isAdmin)
                            <td>
                                <a href="{{ route('staff.edit', $staff->id_staff) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('staff.destroy', $staff->id_staff) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus staff ini?')">Hapus</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($isAdmin)
            <a href="{{ route('staff.create') }}" class="btn btn-primary mt-3">Tambah Staff</a>
            @endif
        </div>
    </div>
</div>
@endsection
