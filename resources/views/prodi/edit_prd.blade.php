@extends('layout.master')
@section('judul', 'Edit Buku')
@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="/prd/update/{{$prd->kode_prodi}}" method="POST">
    <input type="text" name="kode_prodi" placeholder="Masukan Kode Prodi" class="form-control" value="{{$prd->kode_prodi}}" readonly><br>
    <input type="text" name="nama_prodi" placeholder="Masukan Nama Prodi" class="form-control" value="{{$prd->nama_prodi}}"><br>
    <input type="text" name="singkatan" placeholder="Masukan Singkatan" class="form-control" value="{{$prd->singkatan}}"><br>

    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <input type="submit" value="Simpan Data" class="btn btn-primary">

</form>

@endsection

