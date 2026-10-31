<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengelola - Bank Sampah RW 04</title>
</head>
<body>

    <h1>Login Pengelola</h1>
    <p>Bank Sampah RW 04 Cikokol</p>

    @if ($errors->any())
        <p>{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf

        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                value="{{ old('username') }}" required>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <a href="{{ route('dashboard.publik') }}">Kembali ke halaman utama</a>

</body>
</html>