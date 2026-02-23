@extends('layout.master')
@section('judul', 'Form mahasiswa')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="/mhs/simpan" method="POST">
    @csrf
    <input type="text" name="nim" placeholder="Masukan NIM" class="form-control"><br>
    <input type="text" name="nama" placeholder="Masukan Nama" class="form-control"><br>
    <input type="text" name="tempat_lahir" placeholder="Masukan Tempat Lahir" class="form-control"><br>
    <input type="date" name="tgl_lahir" placeholder="Masukan Tanggal Lahir" class="form-control"><br>
    <select name="prodi_id" class="form-control">
        <option selected disabled>Pilih Prodi</option>
        @foreach ($prodi as $p)
            <option value ="{{ $p->kode_prodi }}">{{ $p->nama_prodi }}</option>
        @endforeach
    </select><br>
    <input type="text" name="th_masuk" placeholder="Masukan Tahun Masuk" class="form-control"><br>
    <input type="text" name="role" class="form-control" value="mhs" readonly disabled><br>
    <input type="hidden" name="role" value="mhs">
    <input type="email" name="email" placeholder="Masukan Email" class="form-control"><br>
    <input type="text" name="no_telp" placeholder="Masukan Nomor Telepon" class="form-control"><br>
    <input type="password" name="password" placeholder="Masukan Password" class="form-control"><br>
    <input type="submit" value="Simpan Data" class="btn btn-primary">

</form>

@endsection