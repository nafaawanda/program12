<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body { background: #f8fafc; font-family: Arial, sans-serif; }
        .login-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 32px; }
        h2 { text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; }
        .btn { width: 100%; padding: 10px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .error { color: #dc2626; margin-bottom: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Mahasiswa</h2>
        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        <form action="/mahasiswa/login" method="POST">
            @csrf
            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" id="nim" name="nim" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
