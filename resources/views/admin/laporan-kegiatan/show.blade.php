<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan Kegiatan</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 40px 15px;
            color: white;
            background: linear-gradient(135deg, #244c42, #172c29, #426b59);
        }

        .container {
            max-width: 850px;
            margin: auto;
            padding: 35px;
            border-radius: 25px;
            background: rgba(25, 35, 32, .88);
            border: 1px solid rgba(255,255,255,.15);
            box-shadow: 0 15px 40px rgba(0,0,0,.3);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        .line {
            height: 1px;
            background: rgba(255,255,255,.3);
            margin-bottom: 25px;
        }

        .info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-item {
            padding: 15px;
            border-radius: 13px;
            background: rgba(255,255,255,.08);
            text-align: center;
        }

        .info-item small {
            display: block;
            color: #83e9b2;
            margin-bottom: 8px;
        }

        .description {
            padding: 20px;
            border-radius: 15px;
            background: rgba(255,255,255,.07);
            line-height: 1.8;
            white-space: pre-line;
            margin-bottom: 25px;
        }

        .photo {
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            border-radius: 15px;
            background: rgba(0,0,0,.3);
        }

        .no-photo {
            padding: 45px;
            text-align: center;
            background: rgba(255,255,255,.07);
            border-radius: 15px;
            color: #ccc;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 11px 23px;
            border: none;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .back { background: #52635d; }
        .edit { background: #087b55; }
        .delete { background: #b91c1c; }

        @media(max-width: 600px) {
            .container { padding: 22px; }
            .info { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Detail Laporan Kegiatan</h1>

    <div class="line"></div>

    <h2 style="margin-bottom: 20px;">
        Laporan Minggu {{ $laporan->minggu }}
    </h2>

    <div class="info">

        <div class="info-item">
            <small>Periode</small>
            <strong>
                {{ \Carbon\Carbon::create()->month($laporan->bulan)->translatedFormat('F') }}
                {{ $laporan->tahun }}
            </strong>
        </div>

        <div class="info-item">
            <small>Minggu</small>
            <strong>Minggu ke-{{ $laporan->minggu }}</strong>
        </div>

        <div class="info-item">
            <small>Tanggal Kegiatan</small>
            <strong>{{ $laporan->tanggal->format('d M Y') }}</strong>
        </div>

    </div>

    <h3 style="margin-bottom: 12px;">Deskripsi Kegiatan</h3>

    <div class="description">{{ $laporan->deskripsi }}</div>

    <h3 style="margin-bottom: 12px;">Dokumentasi Kegiatan</h3>

    @if($laporan->foto)
        <img
            src="{{ asset('storage/' . $laporan->foto) }}"
            alt="Foto dokumentasi kegiatan"
            class="photo">
    @else
        <div class="no-photo">
            📷 Belum ada foto dokumentasi.
        </div>
    @endif

    <div class="actions">

        <a href="{{ route('admin.laporan-kegiatan.index') }}"
           class="btn back">
            ← Kembali
        </a>

        <a href="{{ route('admin.laporan-kegiatan.edit', $laporan->id) }}"
           class="btn edit">
            Edit Laporan
        </a>

        <form
            action="{{ route('admin.laporan-kegiatan.destroy', $laporan->id) }}"
            method="POST"
            onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">

            @csrf
            @method('DELETE')

            <button type="submit" class="btn delete">
                Hapus Laporan
            </button>

        </form>

    </div>

</div>

</body>
</html>
