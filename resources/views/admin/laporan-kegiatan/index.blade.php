<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kegiatan - Bank Sampah RW 04</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            color: white;
            background:
                linear-gradient(rgba(10, 25, 24, .65), rgba(10, 25, 24, .75)),
                linear-gradient(135deg, #244c42, #172c29, #426b59);
            background-attachment: fixed;
        }

        .sidebar {
            position: fixed;
            top: 15px;
            left: 15px;
            bottom: 15px;
            width: 75px;
            padding: 15px 8px;
            background: rgba(255,255,255,.9);
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            z-index: 10;
        }

        .logo {
            color: #087b55;
            font-size: 23px;
            font-weight: 800;
        }

        .menu {
            width: 48px;
            padding: 12px 3px;
            border-radius: 13px;
            text-decoration: none;
            text-align: center;
            color: #164b3d;
            font-size: 11px;
            transition: .2s;
        }

        .menu span {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .menu.active,
        .menu:hover {
            background: #075b49;
            color: white;
        }

        .main {
            margin-left: 100px;
            padding: 45px 5%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: clamp(30px, 4vw, 46px);
            margin-bottom: 15px;
        }

        .header p {
            font-size: 17px;
            color: #e0e9e5;
            max-width: 500px;
            margin: auto;
            line-height: 1.6;
        }

        .add-button {
            display: inline-block;
            margin-top: 25px;
            padding: 11px 25px;
            border-radius: 30px;
            color: white;
            background: linear-gradient(90deg, #087b55, #31bd75);
            text-decoration: none;
            font-weight: 600;
        }

        .filter {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin: 35px 0 50px;
        }

        .filter select,
        .filter input {
            min-width: 175px;
            padding: 13px 18px;
            border: 2px solid white;
            border-radius: 30px;
            background: rgba(0,0,0,.65);
            color: white;
            outline: none;
        }

        .filter input::placeholder {
            color: #ddd;
        }

        .filter button {
            padding: 12px 23px;
            border: 2px solid white;
            border-radius: 30px;
            color: white;
            background: #087b55;
            cursor: pointer;
        }

        .success {
            max-width: 1000px;
            margin: 0 auto 20px;
            padding: 13px 18px;
            border-radius: 10px;
            background: rgba(22, 163, 74, .85);
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .report-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px;
            min-height: 160px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 18px;
            background: rgba(25, 35, 32, .76);
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 25px rgba(0,0,0,.2);
        }

        .report-image {
            width: 145px;
            height: 115px;
            flex-shrink: 0;
            border-radius: 13px;
            object-fit: cover;
            background: #52655c;
        }

        .image-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
        }

        .report-content {
            flex: 1;
            min-width: 0;
        }

        .report-content h3 {
            font-size: 17px;
            margin-bottom: 12px;
        }

        .meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            color: #83e9b2;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .report-content p {
            color: #e0e0e0;
            font-size: 13px;
            line-height: 1.5;
        }

        .detail-button {
            position: absolute;
            top: -12px;
            right: 10px;
            padding: 8px 20px;
            border-radius: 25px;
            background: linear-gradient(90deg, #087b55, #52d68c);
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .empty {
            grid-column: 1 / -1;
            padding: 45px 20px;
            text-align: center;
            background: rgba(255,255,255,.1);
            border-radius: 15px;
        }

        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        @media(max-width: 850px) {
            .report-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 600px) {
            .sidebar {
                width: 58px;
                left: 7px;
                top: 7px;
                bottom: 7px;
            }

            .menu {
                width: 42px;
                font-size: 9px;
            }

            .main {
                margin-left: 70px;
                padding: 30px 15px;
            }

            .report-card {
                flex-direction: column;
                align-items: stretch;
            }

            .report-image {
                width: 100%;
                height: 180px;
            }

            .filter {
                flex-direction: column;
            }

            .filter select,
            .filter input,
            .filter button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="logo">EV</div>

        <a href="{{ route('admin.dashboard') }}" class="menu">
            <span>⌂</span>Home
        </a>

        <a href="{{ route('admin.laporan-kegiatan.index') }}" class="menu active">
            <span>▣</span>Reports
        </a>

        <a href="#" class="menu">
            <span>▤</span>Recap
        </a>

        <a href="#" class="menu">
            <span>◉</span>Payout
        </a>
    </aside>

    <main class="main">

        <header class="header">
            <h1>Laporan Kegiatan</h1>
            <p>
                Berikut adalah laporan kegiatan Bank Sampah
                RW 04 Cikokol dalam beberapa periode.
            </p>

            <a href="{{ route('admin.laporan-kegiatan.create') }}"
               class="add-button">
                + Tambahkan Laporan
            </a>
        </header>

        @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.laporan-kegiatan.index') }}"
              method="GET"
              class="filter">

            <select name="bulan">
                <option value="">Semua Periode</option>
                @foreach(range(1, 12) as $bulan)
                    <option value="{{ $bulan }}"
                        @selected(request('bulan') == $bulan)>
                        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>

            <select name="minggu">
                <option value="">Semua Minggu</option>
                @foreach(range(1, 5) as $minggu)
                    <option value="{{ $minggu }}"
                        @selected(request('minggu') == $minggu)>
                        Minggu ke-{{ $minggu }}
                    </option>
                @endforeach
            </select>

            <input type="number"
                   name="tahun"
                   placeholder="Tahun"
                   value="{{ request('tahun') }}">

            <input type="text"
                   name="cari"
                   placeholder="Cari laporan..."
                   value="{{ request('cari') }}">

            <button type="submit">Cari</button>
        </form>

        <section class="report-grid">

            @forelse($laporans as $laporan)

                <article class="report-card">

                    @if($laporan->foto)
                        <img
                            src="{{ asset('storage/' . $laporan->foto) }}"
                            alt="Dokumentasi kegiatan"
                            class="report-image">
                    @else
                        <div class="report-image image-placeholder">
                            📷
                        </div>
                    @endif

                    <div class="report-content">

                        <h3>Laporan Minggu {{ $laporan->minggu }}</h3>

                        <div class="meta">
                            <span>
                                ◷ {{ \Carbon\Carbon::create()->month($laporan->bulan)->translatedFormat('F') }}
                                {{ $laporan->tahun }}
                            </span>

                            <span>
                                Minggu ke-{{ $laporan->minggu }}
                            </span>

                            <span>
                                {{ $laporan->tanggal->format('d M Y') }}
                            </span>
                        </div>

                        <p>
                            {{ \Illuminate\Support\Str::limit($laporan->deskripsi, 110) }}
                        </p>

                    </div>

                    <a href="{{ route('admin.laporan-kegiatan.show', $laporan->id) }}"
                       class="detail-button">
                        Details →
                    </a>

                </article>

            @empty

                <div class="empty">
                    <h3>Belum Ada Laporan Kegiatan</h3>
                    <p>Silakan tambahkan laporan kegiatan pertama.</p>
                </div>

            @endforelse

        </section>

        <div class="pagination">
            {{ $laporans->links() }}
        </div>

    </main>

</body>
</html>
