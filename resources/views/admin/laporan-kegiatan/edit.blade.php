<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan Kegiatan</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
            color: white;
            background: linear-gradient(135deg, #456b5c, #172b27, #5d7565);
        }

        .container {
            width: 100%;
            max-width: 850px;
            padding: 35px 40px;
            border-radius: 30px;
            background: rgba(28, 34, 34, .88);
            border: 1px solid rgba(255,255,255,.15);
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 45px rgba(0,0,0,.3);
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

        .row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
        }

        input, select, textarea {
            width: 100%;
            padding: 13px 15px;
            color: white;
            background: rgba(0,0,0,.6);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            outline: none;
        }

        select option {
            color: white;
            background: #202525;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #35c47d;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
            line-height: 1.6;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .counter {
            text-align: right;
            font-size: 12px;
            margin-top: 5px;
            color: #ddd;
        }

        .photo-preview {
            display: block;
            max-width: 250px;
            max-height: 180px;
            object-fit: cover;
            margin: 10px auto;
            border-radius: 15px;
        }

        .error {
            color: #ffaaaa;
            font-size: 12px;
            margin-top: 6px;
        }

        .validation {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 12px;
            background: rgba(180,30,30,.35);
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }

        .back {
            background: #58645e;
        }

        .save {
            background: linear-gradient(90deg, #087b55, #32c34f);
        }

        @media(max-width: 650px) {
            .container {
                padding: 25px 20px;
            }

            .row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Edit Laporan Mingguan</h1>
    <div class="line"></div>

    @if($errors->any())
        <div class="validation">
            <strong>Periksa kembali data:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.laporan-kegiatan.update', $laporan->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="row">

            <div class="group">
                <label for="bulan">Periode</label>

                <select name="bulan" id="bulan" required>
                    @foreach(range(1, 12) as $bulan)
                        <option value="{{ $bulan }}"
                            @selected(old('bulan', $laporan->bulan) == $bulan)>
                            {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <input type="number"
                       name="tahun"
                       value="{{ old('tahun', $laporan->tahun) }}"
                       min="2020"
                       max="2100"
                       style="margin-top:8px;"
                       required>
            </div>

            <div class="group">
                <label for="minggu">Minggu</label>

                <select name="minggu" id="minggu" required>
                    @foreach(range(1, 5) as $minggu)
                        <option value="{{ $minggu }}"
                            @selected(old('minggu', $laporan->minggu) == $minggu)>
                            Minggu ke-{{ $minggu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="group">
                <label for="tanggal">Tanggal</label>

                <input type="date"
                       name="tanggal"
                       id="tanggal"
                       value="{{ old('tanggal', $laporan->tanggal->format('Y-m-d')) }}"
                       required>
            </div>

        </div>

        <div class="line"></div>

        <div class="group">
            <label for="deskripsi">Deskripsi Kegiatan</label>

            <textarea name="deskripsi"
                      id="deskripsi"
                      maxlength="500"
                      required>{{ old('deskripsi', $laporan->deskripsi) }}</textarea>

            <div class="counter">
                <span id="char-count">0</span>/500
            </div>
        </div>

        <div class="line"></div>

        <div class="group">
            <label>Dokumentasi Saat Ini</label>

            @if($laporan->foto)
                <img
                    src="{{ asset('storage/' . $laporan->foto) }}"
                    class="photo-preview"
                    alt="Foto dokumentasi saat ini">
            @else
                <p style="text-align:center;color:#ccc;">
                    Belum ada foto dokumentasi.
                </p>
            @endif
        </div>

        <div class="group">
            <label for="foto">Ganti Foto Dokumentasi (Opsional)</label>

            <input type="file"
                   name="foto"
                   id="foto"
                   accept=".jpg,.jpeg,.png">

            <img id="preview"
                 class="photo-preview"
                 style="display:none;"
                 alt="Preview foto baru">

            <small style="display:block;text-align:center;color:#bbb;margin-top:8px;">
                Format JPG/PNG, maksimal 5 MB
            </small>
        </div>

        <div class="buttons">

            <a href="{{ route('admin.laporan-kegiatan.show', $laporan->id) }}"
               class="btn back">
                Kembali
            </a>

            <button type="submit" class="btn save">
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

<script>
    const deskripsi = document.getElementById('deskripsi');
    const count = document.getElementById('char-count');

    function updateCounter() {
        count.textContent = deskripsi.value.length;
    }

    deskripsi.addEventListener('input', updateCounter);
    updateCounter();

    const foto = document.getElementById('foto');
    const preview = document.getElementById('preview');

    foto.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Format foto harus JPG atau PNG.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran foto maksimal 5 MB.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });
</script>

</body>
</html>
