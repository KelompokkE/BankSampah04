<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bank Sampah RW 04</title>
</head>
<body>

    <h1>Dashboard Pengelola</h1>
    <p>Bank Sampah RW 04 Cikokol</p>

    <h2>Selamat Datang, Pengelola!</h2>
    <p>Silakan pilih menu untuk mengelola data bank sampah.</p>

    <ul>
        <li>Data Nasabah</li>
        <li>Penimbangan</li>
        <li>Laporan Kegiatan</li>
        <li>Rekap Bulanan</li>
        <li>Penyaluran Hasil</li>
    </ul>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>