@extends('layout.master')

@section('judul', 'Dashboard Administrator')

@section('content')
<div class="container-fluid">
    <?php $admin = auth('admin')->user(); ?>
    <h1 class="h3 mb-4 text-gray-800">Selamat Datang, {{ $admin->nama ?? 'Administrator' }}!</h1>
    
    <div class="row">
        <!-- Mahasiswa Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mahasiswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/mhs/show" class="btn btn-primary btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Administrator</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/admin/show" class="btn btn-danger btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Staff</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/staff/show" class="btn btn-warning btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buku Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Buku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/bk/show" class="btn btn-success btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prodi Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Prodi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/prd/show" class="btn btn-info btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peminjaman Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Peminjaman</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/pinjam" class="btn btn-secondary btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengembalian Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Pengembalian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/kembali" class="btn btn-dark btn-sm">Kelola</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Laporan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="/laporan/pengembalian" class="btn btn-primary btn-sm">Lihat</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                <div class="col-md-3">
                    <a href="/mhs/show" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-users"></i> Kelola Mahasiswa
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/admin/show" class="btn btn-outline-danger btn-block mb-2">
                        <i class="fas fa-user-shield"></i> Kelola Administrator
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/staff/show" class="btn btn-outline-warning btn-block mb-2">
                        <i class="fas fa-user-tie"></i> Kelola Staff
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/bk/show" class="btn btn-outline-success btn-block mb-2">
                        <i class="fas fa-book"></i> Kelola Buku
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/prd/show" class="btn btn-outline-info btn-block mb-2">
                        <i class="fas fa-school"></i> Kelola Prodi
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/pinjam" class="btn btn-outline-secondary btn-block mb-2">
                        <i class="fas fa-handshake"></i> Peminjaman
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/kembali" class="btn btn-outline-dark btn-block mb-2">
                        <i class="fas fa-undo"></i> Pengembalian
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('profil') }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-user"></i> Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> Informasi</h5>
        <p class="mb-0">Anda login sebagai Administrator. Gunakan menu di sidebar untuk navigasi lengkap.</p>
    </div>
</div>
@endsection
