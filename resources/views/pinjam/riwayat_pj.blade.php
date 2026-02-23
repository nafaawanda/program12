@extends('layout.master')
@section('judul','Riwayat Peminjaman')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Peminjaman Anda</h6>
        @if ($mahasiswa)
            <span class="badge badge-info">{{ $mahasiswa->nama }} ({{ $mahasiswa->nim }})</span>
        @else
            <span class="badge badge-info">KOSONG</span>
        @endif
    </div>
    <div class="card-body">
        @if ($riwayat->isEmpty())
            <p class="text-center mb-0">Belum ada riwayat peminjaman.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Detail Buku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $index => $pinjam)
                            @php
                                $isTelat = isset($pinjam->is_telat) && $pinjam->is_telat;
                                $tglKembali = \Carbon\Carbon::parse($pinjam->tgl_kembali);
                                $today = \Carbon\Carbon::today();
                                $isOverdue = $tglKembali->lt($today) && $pinjam->detil->where('status', 1)->isNotEmpty();
                                $cekstatus = $pinjam->detil->where('status', 1)->isNotEmpty();
                            @endphp
                            <tr class="{{ $isOverdue ?   'table-danger' :  ($cekstatus?'table-warning' : 'table-success') }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y') }}</td>
                                <td class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($pinjam->tgl_kembali)->format('d M Y') }}
                                    @if ($isOverdue)
                                        <span class="badge badge-danger ml-2">TERLAMBAT2</span>
                                    @endif
                                </td>
                                <td>
                                    <ul class="mb-0 pl-3">
                                        @foreach ($pinjam->detil as $detil)
                                            @php
                                                $detilIsOverdue = $isOverdue && $detil->status == 1;
                                            @endphp
                                            <li>
                                                <strong>{{ $detil->judul_buku }}</strong> ({{ $detil->kode_buku }}) -
                                                {{ $detil->jml_buku }} buku
                                                <span class="badge {{ $detilIsOverdue ? 'badge-danger' : ($detil->status ? 'badge-warning' : 'badge-success') }}">
                                                    @if ($detilIsOverdue)
                                                        TERLAMBAT1
                                                    @else
                                                        {{ $detil->status ? 'Sedang dipinjam' : 'Sudah dikembalikan' }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

