@extends('layout.master')

@section('judul', 'Settings - Ubah Password')

@section('content')
<div class="container mt-4">
    <h3>Settings</h3>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-key"></i> Ubah Password</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="current_password">Password Lama</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                    @error('current_password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                    @error('new_password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
