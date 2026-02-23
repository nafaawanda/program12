@extends('layout.master')
@section('judul','Peminjaman')
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
    $user = auth('mahasiswas')->user();
    $staff = auth('staff')->user();
    $admin = auth('admin')->user();
    
    // Determine the form action based on logged in user
    if ($staff || $admin) {
        $formAction = '/staff/pinjam/simpan';
    } else {
        $formAction = '/pinjam/simpan';
    }
@endphp
<form action = "{{ $formAction }}" method="POST">
<div class="card">
    <div class="card-header">Cari Peminjam</div>
    <div class="card-body">
        <!-- Search semua peminjam (mahasiswa, admin, staff) -->
        <div class="form-group">
            <label>Cari Peminjam (Nama Mahasiswa, Admin, atau Staff)</label>
            <input type="text" class="form-control" placeholder="Ketik nama..." id="cariPeminjam" name="cariPeminjam">
            <small class="text-muted">Ketik nama untuk mencari di semua kategori</small>
        </div>
        
        <!-- Hidden field untuk menyimpan borrower_type yang terdeteksi -->
        <input type="hidden" id="borrower_type" name="borrower_type" value="mahasiswa">
        
        <!-- Field NIM untuk Mahasiswa -->
        <div class="form-group">
            <label>NIM / ID Peminjam</label>
            <input type="text" class="form-control" id="nim" name="nim" readonly="" placeholder="Akan terisi otomatis setelah mencari">
        </div>
        
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </div>
    <div class="card shadow mb-4" id="buku_form" style="display:none;">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        Cari Buku
                        <input type="text" class="form-control form-control-user" id="caribuku" name="caribuku"
                            placeholder="Cari buku...." value="">
                    </div>

                    <div class="form-group">
                        Kode Buku
                        <input type="text" class="form-control form-control-user" id="kode_buku" name="kode_buku"
                            placeholder="Kode Buku" value="" readonly>
                    </div>
                    <div class="form-group">
                        Nama Penulis
                        <input type="text" class="form-control form-control-user" id="nama_penulis" name="nama_penulis"
                            placeholder="Nama Penulis" value="" readonly>
                    </div>
                    <div class="form-group">
                        Jenis Buku
                        <input type="text" class="form-control form-control-user" id="jenis_buku" name="jenis_buku"
                            placeholder="Jenis Buku" value="" readonly>
                    </div>
                    <div class="form-group">
                        Kode Rak
                        <input type="text" class="form-control form-control-user" id="kode_rak" name="kode_rak"
                            placeholder="Kode Rak" value="" readonly>
                    </div>
                    <div class="form-group">
                        Tanggal Pinjam
                        <input type="text" class="form-control form-control-user" id="tgl_pinjam"
                            name="tgl_pinjam" placeholder="Kode Buku" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        Petugas
                        @php
                            $petugas = auth('staff')->user();
                            $admin = auth('admin')->user();
                            if (!$petugas && $admin) {
                                $petugas = (object)[
                                    'id_staff' => $admin->id,
                                    'nama' => $admin->nama,
                                    'role' => 'admin'
                                ];
                            }
                        @endphp
                        @if($petugas)
                            <input type="hidden" id="pegawai_id" name="pegawai_id" value="{{ $petugas->id_staff }}">
                            <input type="text" class="form-control form-control-user" id="petugas_nama" name="petugas_nama"
                                placeholder="Petugas" value="{{ $petugas->nama }} (ID: {{ $petugas->id_staff }}) - Role: {{ $petugas->role ?? 'petugas' }}" readonly>
                        @else
                            <input type="hidden" id="pegawai_id" name="pegawai_id" value="">
                            <input type="text" class="form-control form-control-user" id="petugas_nama" name="petugas_nama"
                                placeholder="Petugas" value="" readonly>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        Jumlah Peminjaman
                        <input type="text" class="form-control form-control-user" id="jumlah_pinjam" name="jumlah_pinjam"
                            placeholder="Jumlah dipinjam" value="1">
                    </div>
                    <div class="form-group">
                        Stok
                        <input type="text" class="form-control form-control-user" id="stock" name="stock"
                            placeholder="Stok Buku" value="" readonly>
                    </div>
                    <div class="form-group">
                        Tanggal Kembali
                        <input type="text" class="form-control form-control-user" id="tgl_kembali"
                            name="tgl_kembali" placeholder="Kode Buku"
                            value="{{ date('Y-m-d', strtotime('+6 day', time())) }}">
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-info" id="tambah_tabel">
                Tambah Keranjang
            </button>

        </div>

        <table id="tabelPinjam" name="tabelPinjam" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Buku</th>
                    <th>Kode Buku</th>
                    <th>Nama Penulis</th>
                    <th>Jenis Buku</th>
                    <th>Kode Rak</th>
                    <th>Stok Buku</th>
                    <th>Jumlah dipinjam</th>
                    <th>Sisa</th>
                    <!-- <th>Status</th> -->

                </tr>
            </thead>

            <tbody id="template">

            </tbody>
        </table>
        <div class="card-body">
            <button type="submit" class="btn btn-info">
                Pinjam
            </button>

            </a>
        </div>

    </div>
</div>
</form>

<!-- Tabel Daftar Peminjaman untuk Admin
@php
    $userRole = auth('mahasiswas')->check() ? auth('mahasiswas')->user()->role : null;
@endphp

@if ($userRole === 'admin' && isset($pj) && $pj->isNotEmpty())
<div class="card shadow mb-4 mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Peminjaman</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Peminjam</th>
                        <th>Role</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Total Buku</th>
                        <!-- <th>Status</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pj as $index => $p)
                    <tr class="{{ $p->is_telat ? 'table-danger' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->nim }}</td>
                        <td>{{ $p->mahasiswa_nama ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $p->mahasiswa_role === 'admin' ? 'badge-primary' : 'badge-info' }}">
                                {{ $p->mahasiswa_role ?? '-' }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}</td>
                        <td class="{{ $p->is_telat ? 'text-danger font-weight-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($p->tgl_kembali)->format('d M Y') }}
                            @if ($p->is_telat)
                                <span class="badge badge-danger ml-2">TERLAMBAT</span>
                            @endif
                        </td>
                        <td>{{ $p->total_buku }}</td>
                        <td>
                            @if ($p->is_telat)
                                <span class="badge badge-danger">Terlambat</span>
                            @else
                                <span class="badge badge-success">Tepat Waktu</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> -->
@endif
@endsection

@push('styles')
<style>
    .ui-autocomplete {
        background: #e3f2fd !important; /* biru muda */
        color: #222;
        border-radius: 6px;
        border: 1px solid #90caf9;
        z-index: 9999 !important;
        min-width: 0 !important;
        width: auto !important;
        max-width: none !important;
        box-sizing: border-box !important;
    }
    .ui-menu-item-wrapper.ui-state-active {
        background: #1976d2 !important; /* biru lebih gelap saat hover */
        color: #fff !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Agar lebar dropdown autocomplete mengikuti input
    $.ui.autocomplete.prototype._resizeMenu = function() {
        var ul = this.menu.element;
        var input = this.element;
        var width = input.outerWidth();
        ul.css({
            'width': width + 'px',
            'max-width': width + 'px'
        });
    }
});
</script>
@endpush
@push('Java')
<script type="text/javascript">
    // Function to handle autocomplete peminjam (searches all: mahasiswa, admin, staff)
    $(document).ready(function() {
        $('#cariPeminjam').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '{{ $staff ? "/staff/autocomplete-peminjam" : "/autocomplete-peminjam" }}',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        console.log('Results:', data);
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error: ' + error);
                        alert('Error fetching data: ' + error);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                // Set the nim/id field
                $('#nim').val(ui.item.id);
                // Set the borrower_type hidden field
                $('#borrower_type').val(ui.item.type);
                // Show selected info in input
                $('#cariPeminjam').val(ui.item.label);
                // Show the book form
                $('#buku_form').show();
                return false;
            }
        });
    });
    
    // Autocomplete Buku
    $('#caribuku').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ $staff ? "/staff/autocomplete-buku" : "/autocomplete-buku" }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    term: request.term,
                    _token: $('input[name=_token]').val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            $('#kode_buku').val(ui.item.value);
            $('#stock').val(ui.item.stock);
            $('#nama_penulis').val(ui.item.nama_penulis || '');
            $('#jenis_buku').val(ui.item.jenis_buku || '');
            $('#kode_rak').val(ui.item.kode_rak || '');
            $('#caribuku').val(ui.item.label);
            return false;
        }
    });

        // ============================================
        // FUNGSI TAMBAH BUKU KE KERANJANG (TABEL)
        // ============================================
        // Fungsi ini dipanggil ketika user klik tombol "Tambah Keranjang"
        // Berfungsi untuk menambahkan buku ke tabel keranjang sebelum submit
        $('#tambah_tabel').click(function() {
            // ============================================
            // BAGIAN 1: AMBIL DATA DARI FORM INPUT
            // ============================================
            var no = $('#template tr').length + 1; // Otomatis menghitung nomor urut berdasarkan jumlah baris yang sudah ada
            let nama_buku = $('#caribuku').val(); // Ambil nama buku dari input field
            var kode_buku = $('#kode_buku').val(); // Ambil kode buku dari input field
            var nama_penulis = $('#nama_penulis').val(); // Ambil nama penulis dari input field
            var jenis_buku = $('#jenis_buku').val(); // Ambil jenis buku dari input field
            var kode_rak = $('#kode_rak').val(); // Ambil kode rak dari input field
            var stock = parseInt($('#stock').val()) || 0; // Ambil stok buku dan konversi ke integer, jika kosong maka 0
            // Mengambil jumlah peminjaman dari input field jumlah_pinjam dan dikonversi ke integer
            // parseInt() adalah fungsi JavaScript untuk mengkonversi string ke integer
            // Jika tidak ada nilai atau null, maka diisi dengan 0 (menggunakan operator ||)
            var jumlah_pinjam = parseInt($('#jumlah_pinjam').val()) || 0;
            // var status = 1; // Status default: 1 = masih dipinjam

            // ============================================
            // BAGIAN 2: VALIDASI CLIENT-SIDE (SEBELUM TAMBAH KE TABEL)
            // ============================================
            
            // VALIDASI 1: Cek apakah jumlah peminjaman lebih dari 0
            // Validasi ini mencegah user memasukkan jumlah 0 atau negatif
            if (jumlah_pinjam <= 0) {
                alert('Jumlah peminjaman harus lebih dari 0!');
                return false; // Hentikan proses, jangan tambahkan ke tabel
            }

            // VALIDASI 2: Cek apakah jumlah peminjaman melebihi stok yang tersedia
            // Ini adalah validasi utama untuk mencegah peminjaman melebihi stok
            // Alert akan muncul jika user mencoba meminjam lebih dari stok yang ada
            if (jumlah_pinjam > stock) {
                alert('Jumlah peminjaman (' + jumlah_pinjam + ') tidak boleh melebihi stok yang tersedia (' + stock + ')!');
                return false; // Hentikan proses, jangan tambahkan ke tabel
            }

            // VALIDASI 3: Cek apakah buku sudah ada di tabel (untuk menghindari duplikasi)
            // Mencegah user menambahkan buku yang sama dua kali di keranjang
            var kodeBukuSudahAda = false;
            // Loop melalui setiap baris di tabel untuk mengecek duplikasi
            $('#template tr').each(function() {
                var kodeBukuTabel = $(this).find('input[name="kode_buku[]"]').val();
                if (kodeBukuTabel === kode_buku) {
                    kodeBukuSudahAda = true;
                    return false; // break loop jika sudah ditemukan
                }
            });

            // Jika buku sudah ada di keranjang, tampilkan alert dan hentikan proses
            if (kodeBukuSudahAda) {
                alert('Buku dengan kode ' + kode_buku + ' sudah ada di keranjang!');
                return false; // Hentikan proses
            }

            // ============================================
            // BAGIAN 3: HITUNG SISA STOK
            // ============================================
            // Hitung sisa stok setelah peminjaman
            // Sisa stok = stok awal - jumlah yang dipinjam
            var sisa = stock - jumlah_pinjam;
            // ============================================
            // BAGIAN 4: TAMBAHKAN BARIS BARU KE TABEL
            // ============================================
            // Buat HTML untuk baris baru di tabel keranjang
            // Baris ini akan berisi data buku yang akan dipinjam
            var newRow = '<tr>' +
                '<td>' + no + '</td>' + // Nomor urut
                '<td><div style="white-space:normal;min-width:120px;max-width:400px;display:inline-block;">' + nama_buku + '<input type="hidden" name="nama_buku[]" value="' + nama_buku + '"></div></td>' + // Nama buku (auto width)
                '<td><input type="text" class="form-control-plaintext" value="' + kode_buku + '" name="kode_buku[]" readonly></td>' + // Kode buku (readonly, akan dikirim ke server)
                '<td><div style="white-space:normal;min-width:120px;max-width:400px;display:inline-block;">' + (nama_penulis || '') + '<input type="hidden" name="nama_penulis[]" value="' + (nama_penulis || '') + '"></div></td>' + // Nama penulis (auto width)
                '<td><div style="white-space:normal;min-width:120px;max-width:400px;display:inline-block;">' + (jenis_buku || '') + '<input type="hidden" name="jenis_buku[]" value="' + (jenis_buku || '') + '"></div></td>' + // Jenis buku (auto width)
                '<td><div style="white-space:normal;min-width:120px;max-width:400px;display:inline-block;">' + (kode_rak || '') + '<input type="hidden" name="kode_rak[]" value="' + (kode_rak || '') + '"></div></td>' + // Kode rak (auto width)
                '<td><input type="text" class="form-control-plaintext" value="' + stock + '" name="stock[]" readonly></td>' + // Stok awal (readonly)
                '<td><input type="text" class="form-control-plaintext" value="' + jumlah_pinjam + '" name="jumlah_pinjam[]" readonly></td>' + // Jumlah pinjam (readonly, akan dikirim ke server)
                '<td><input type="text" class="form-control-plaintext" value="' + sisa + '" name="sisa[]" readonly></td>' + // Sisa stok setelah pinjam (readonly)
                '<input type="hidden" name="status[]" value="1">' + // Status: 1 = masih dipinjam
                '<td><button type="button" class="btn btn-danger btn-sm btn-hapus"><i class="fas fa-trash"></i> Delete</button></td>' + // Tombol hapus
                '</tr>';

            // Tambahkan baris baru ke tabel dengan ID 'template'
            $('#template').append(newRow);

            // ============================================
            // BAGIAN 5: RESET FORM INPUT
            // ============================================
            // Setelah berhasil menambahkan ke tabel, reset semua input field
            // agar siap untuk input buku berikutnya
            $('#caribuku').val('').focus(); // Reset dan fokus ke field cari buku
            $('#kode_buku').val(''); // Reset kode buku
            $('#nama_penulis').val(''); // Reset nama penulis
            $('#jenis_buku').val(''); // Reset jenis buku
            $('#kode_rak').val(''); // Reset kode rak
            $('#stock').val(''); // Reset stok
            $('#jumlah_pinjam').val(''); // Reset jumlah pinjam

            // ============================================
            // BAGIAN 6: FUNGSI HAPUS BARIS DARI TABEL
            // ============================================
            // Event handler untuk tombol hapus (delete) di setiap baris tabel
            // Menggunakan event delegation agar bisa bekerja untuk elemen yang ditambahkan dinamis
            $(document).on('click', '.btn-hapus', function() {
                $(this).closest('tr').remove(); // Hapus baris yang berisi tombol hapus yang diklik
                
                // Update nomor urut setelah menghapus baris
                // Loop melalui semua baris dan update nomor urut secara berurutan
                $('#template tr').each(function(index) {
                    $(this).find('td:first').text(index + 1); // Set nomor urut mulai dari 1
                });
            });

    });
</script>
@endpush