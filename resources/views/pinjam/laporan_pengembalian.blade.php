{{-- Menggunakan layout master sebagai template utama untuk konsistensi tampilan di seluruh halaman aplikasi --}}
@extends('layout.master')
{{-- Menentukan judul halaman yang akan ditampilkan di bagian header layout master --}}
@section('judul','Laporan Pengembalian Buku')
{{-- Bagian utama yang berisi seluruh konten halaman laporan pengembalian buku --}}
@section('content')

{{-- Menampilkan pesan sukses jika ada aksi yang berhasil, misal pengembalian buku --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

<!-- Statistik Card -->
{{-- Bagian statistik: menampilkan ringkasan data peminjaman dalam bentuk 4 card agar admin/user bisa melihat status peminjaman secara cepat --}}
<div class="row mb-4"> {{-- Baris berisi 4 card statistik peminjaman --}}
    <div class="col-xl-3 col-md-6 mb-4"> {{-- Card: Total Peminjaman --}}
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Peminjaman</div>  {{-- Menampilkan total peminjaman dari data statistik yang dikirim controller --}}
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_peminjaman'] }}</div> {{-- Nilai total peminjaman --}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4"> {{-- Card: Belum Dikembalikan --}}
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Belum Dikembalikan</div> {{-- Menampilkan jumlah buku yang belum dikembalikan --}}
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_belum_kembali'] }}</div> {{-- Nilai belum dikembalikan --}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4"> {{-- Card: Terlambat --}}
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Terlambat</div> {{-- Menampilkan jumlah peminjaman yang terlambat dikembalikan --}}
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_telat'] }}</div> {{-- Nilai terlambat --}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4"> {{-- Card: Sudah Dikembalikan --}}
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sudah Dikembalikan</div> {{-- Menampilkan jumlah buku yang sudah dikembalikan --}}
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_sudah_kembali'] }}</div> {{-- Nilai sudah dikembalikan --}}
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
{{-- Bagian utama tabel laporan: menampilkan data detail peminjaman dan pengembalian semua user, agar admin/user bisa memantau status buku --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center"> {{-- Header tabel laporan, judul dan tombol cetak (jika diaktifkan) --}}
        <h6 class="m-0 font-weight-bold text-primary">Laporan Peminjaman Buku Semua User</h6>
        <div>
            <!-- <button class="btn btn-sm btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button> -->
        </div>
    </div>
    <div class="card-body">
        {{-- Pencarian cepat di tabel (client-side) --}}
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="text" id="laporanSearch" class="form-control" placeholder="Cari: NIM, Nama Mahasiswa, Admin, Kode/Judul Buku...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="btnCariLaporan">Cari</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnResetLaporan">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($laporan->isEmpty()) {{-- Jika tidak ada data laporan, tampilkan pesan agar user tahu tidak ada peminjaman --}}
            <p class="text-center mb-0">Belum ada data peminjaman.</p> {{-- Pesan jika tidak ada data --}}
        @else
            <div class="table-responsive"> {{-- Membuat tabel responsif agar tampilan tetap baik di berbagai ukuran layar --}}
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light"> {{-- Header kolom tabel, penjelasan setiap field data --}}
                        <tr> {{-- Baris header kolom --}}
                            <th>No</th>
                            <th>NIM</th>
                            <th>Peminjam</th>
                            <th>Pegawai</th>
                            <th>Pegawai ID</th>
                            <th>Kode Buku</th>
                            <th>Judul Buku</th>
                            <th>Kode Rak</th>
                            <th>Jumlah</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody> {{-- Isi tabel, berisi data laporan dari controller --}}
                        {{-- Looping data laporan, setiap baris adalah satu peminjaman/pengembalian --}}
                        @foreach ($laporan as $index => $item) {{-- Loop setiap data laporan, satu baris per peminjaman/pengembalian --}}
                        <tr class="{{ $item->is_telat ? 'table-danger' : ($item->status == 0 ? 'table-success' : '') }}"> {{-- Baris diberi warna: merah jika terlambat, hijau jika sudah dikembalikan --}}
                            <td>{{ $index + 1 }}</td> {{-- Nomor urut data laporan --}}
                            <td>{{ $item->nim }}</td> {{-- NIM mahasiswa yang meminjam --}}
                            <td>{{ $item->nama_mahasiswa }}
                                @if(isset($item->status_mahasiswa))
                                    @if($item->status_mahasiswa == 1)
                                        <span class="badge badge-success ml-1">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary ml-1">Non-Aktif</span>
                                    @endif
                                @endif
                                <span class="badge {{ $item->role === 'admin' ? 'badge-primary' : 'badge-info' }} ml-1">{{ $item->role }}</span>
                            </td>
                            <td>
                                {{ $item->nama_pegawai ?? '-' }}
                                @if(!empty($item->label_pegawai_type))
                                    <span class="badge badge-secondary ml-1">{{ $item->label_pegawai_type }}</span>
                                @endif
                            </td>
                            <td>{{ $item->pegawai_id ?? '-' }}</td>
                            <td>{{ $item->kode_buku }}</td> {{-- Kode buku yang dipinjam oleh user --}}
                            <td>{{ $item->nama_buku }}</td> {{-- Nama/judul buku yang dipinjam --}}
                            <td>{{ $item->kode_rak ?? '-' }}</td> {{-- Kode rak --}}
                            <td>{{ $item->jml_buku }}</td> {{-- Jumlah buku yang dipinjam pada transaksi tersebut --}}
                            <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td> {{-- Tanggal peminjaman buku --}}
                            <td class="{{ $item->is_telat ? 'text-danger font-weight-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }} {{-- Tanggal pengembalian buku --}}
                                @if ($item->is_telat)
                                    <span class="badge badge-danger ml-1"></span> {{-- Badge jika terlambat, agar mudah dikenali admin/user --}}
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 2)
                                    <span class="badge badge-dark">Hilang</span> {{-- Status: buku hilang --}}
                                @elseif ($item->status == 1)
                                    @if ($item->is_telat)
                                        <span class="badge badge-danger">Terlambat Mengemblikan</span> {{-- Status: buku terlambat dikembalikan --}}
                                    @else
                                        <span class="badge badge-warning">Dipinjam</span> {{-- Status: buku masih dipinjam --}}
                                    @endif
                                @else
                                    <span class="badge badge-success">Dikembalikan</span> {{-- Status: buku sudah dikembalikan --}}
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 2)
                                    <span class="text-dark">
                                        <i class="fas fa-times-circle"></i> hilang {{-- Keterangan: buku hilang --}}
                                    </span>
                                @elseif ($item->is_telat)
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat 
                                        {{ \Carbon\Carbon::parse($item->tgl_kembali)->diffForHumans() }} {{-- Keterangan detail: berapa lama keterlambatan pengembalian --}}
                                    </span>
                                @elseif ($item->status == 1)
                                    <span class="text-info">
                                        <i class="fas fa-clock"></i> dipinjam {{-- Keterangan: buku masih dipinjam oleh user --}}
                                    </span>
                                @else
                                    <span class="text-success">
                                        <i class="fas fa-check"></i> dikembalikan {{-- Keterangan: buku sudah dikembalikan --}}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    /* Bagian ini untuk mengatur tampilan saat halaman dicetak, agar hanya tabel laporan yang muncul dan elemen lain disembunyikan */
    .card-header .btn,
    .sidebar,
    .topbar,
    .navbar {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

@endsection

@push('Java')
<script>
    (function() {
        function applyLaporanFilter(term) {
            const tbody = document.querySelector('#dataTable tbody');
            if (!tbody) return;

            const q = (term || '').toString().toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr');            rows.forEach((tr) => {
                const text = (tr.innerText || tr.textContent || '').toLowerCase();
                tr.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('laporanSearch');
            const btnCari = document.getElementById('btnCariLaporan');
            const btnReset = document.getElementById('btnResetLaporan');

            if (btnCari && input) {
                btnCari.addEventListener('click', function() {
                    applyLaporanFilter(input.value);
                });
            }

            if (btnReset && input) {
                btnReset.addEventListener('click', function() {
                    input.value = '';
                    applyLaporanFilter('');
                });
            }

            if (input) {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyLaporanFilter(input.value);
                    }
                });
            }
        });
    })();
</script>
@endpush