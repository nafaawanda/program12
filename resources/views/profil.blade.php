@extends('layout.master')
@section('judul', 'Profil Saya')
@section('content')
<div class="container mt-4">
    <h3>Profil Pengguna</h3>
    <table class="table table-bordered w-50">
        <tr>
            <th>Nama</th>
            <td>{{ $user->nama ?? $user->name }}</td>
        </tr>
        @if(isset($user->nim))
        <tr>
            <th>NIM</th>
            <td>{{ $user->nim }}</td>
        </tr>
        @endif
        @if(isset($user->email))
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        @endif
        @if(isset($user->no_telp))
        <tr>
            <th>No. Telp</th>
            <td>{{ $user->no_telp }}</td>
        </tr>
        @endif
        @if(isset($user->alamat))
        <tr>
            <th>Alamat</th>
            <td>{{ $user->alamat }}</td>
        </tr>
        @endif
        @if(isset($user->role))
        <tr>
            <th>Role</th>
            <td>{{ $user->role }}</td>
        </tr>
        @endif
    </table>
</div>
@endsection
