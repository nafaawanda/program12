@extends('layout.master')
@section('judul', 'Login')
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
<form action="/login" method="POST">
    @csrf
    <input type="text" name="identity" placeholder="Email atau NIM" class="form-control"><br>
    <input type="password" name="password" placeholder="Password" class="form-control"><br>
    <input type="submit" value="Login" class="btn btn-primary">
</form>
@endsection
