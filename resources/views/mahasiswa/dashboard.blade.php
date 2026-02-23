@extends('layout.master')

@section('judul', 'Dashboard Mahasiswa')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Selamat Datang, {{ $user->nama ?? $user->name }}!</h1>
    
    <div class="row">
        <!-- Buku Tersedia Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Buku Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('bk.index') }}" class="btn btn-primary btn-sm">Lihat Buku</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peminjaman Aktif Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Riwayat Peminjaman</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('pinjam.riwayat') }}" class="btn btn-success btn-sm">Lihat Riwayat</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profil Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profil Saya</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('profil') }}" class="btn btn-info btn-sm">Lihat Profil</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Menu Cepat</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('bk.index') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-book"></i> Cari & Pinjam Buku
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('pinjam.riwayat') }}" class="btn btn-outline-secondary btn-block mb-2">
                        <i class="fas fa-history"></i> Lihat Riwayat Peminjaman
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('profil') }}" class="btn btn-outline-info btn-block mb-2">
                        <i class="fas fa-user"></i> Profil Saya
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('settings') }}" class="btn btn-outline-warning btn-block mb-2">
                        <i class="fas fa-cogs"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Informasi</h5>
        <p class="mb-0">Silakan gunakan menu di sidebar untuk navigasi. Anda dapat mencari buku, melihat riwayat peminjaman, dan mengelola profil Anda.</p>
    </div>
</div>
@endsection
