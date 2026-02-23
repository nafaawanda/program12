@extends('layout.master')

@section('judul', 'Edit Staff')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Staff</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            @if(isset($staff) && $staff->id_staff)
                <form action="{{ route('staff.update', $staff->id_staff) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ $staff->nama }}" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ $staff->alamat }}" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telp</label>
                        <input type="text" name="no_telp" class="form-control" value="{{ $staff->no_telp }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                    </div>
                    <div class="form-group">
                        <label>Password (isi jika ingin ganti)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            @else
                <div class="alert alert-danger">Data staff tidak ditemukan.</div>
            @endif
        </div>
    </div>
</div>
@endsection
