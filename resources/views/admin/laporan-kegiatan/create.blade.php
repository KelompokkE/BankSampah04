<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Laporan Mingguan - Bank Sampah RW 04</title>

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
            padding: 35px 15px;
            color: white;
            background:
                linear-gradient(rgba(8, 20, 19, .72), rgba(8, 20, 19, .78)),
                linear-gradient(135deg, #456b5c, #172b27, #5d7565);
        }

        .form-container {
            position: relative;
            width: 100%;
            max-width: 850px;
            padding: 28px 45px;
            border-radius: 38px;
            background: rgba(28, 34, 34, .83);
            border: 1px solid rgba(255,255,255,.13);
            backdrop-filter: blur(18px);
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }

        .back-button {
            position: absolute;
            left: -27px;
            top: 45px;
            width: 55px;
            height: 42px;
            display: grid;
            place-items: center;
            background: #a90000;
            color: white;
            border-radius: 25px 0 0 25px;
            text-decoration: none;
            font-size: 25px;
        }

        h1 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 17px;
        }

        .line {
            height: 1px;
            background: rgba(255,255,255,.4);
            margin-bottom: 30px;
        }

        .period-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            margin-bottom: 23px;
        }

        label {
            display: block;
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
        }

        input, select, textarea {
            width: 100%;
            color: white;
            background: rgba(0,0,0,.65);
            border: 1px solid rgba(255,255,255,.12);
            outline: none;
            border-radius: 25px;
            padding: 12px 16px;
            font-size: 14px;
        }

        select option {
            color: white;
            background: #202525;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        input:focus, select:focus, textarea:focus {
            border-color: #35c47d;
            box-shadow: 0 0 0 2px rgba(53,196,125,.15);
        }

        .year-input {
            margin-top: 8px;
            text-align: center;
        }

        .section-line {
            height: 1px;
            background: rgba(255,255,255,.3);
            margin: 7px 0 22px;
        }

        textarea {
            min-height: 135px;
            resize: vertical;
            border-radius: 25px;
            padding: 18px 20px;
            line-height: 1.5;
        }

        textarea::placeholder {
            color: #c8c8c8;
        }

        .counter {
            text-align: right;
            font-size: 12px;
            margin-top: 5px;
            color: #ddd;
        }

        .upload-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .upload-box {
            min-height: 120px;
            border-radius: 25px;
            background: rgba(0,0,0,.58);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            overflow: hidden;
        }

        .upload-box:hover {
            border: 1px solid #35c47d;
        }

        .upload-icon {
            font-size: 28px;
            color: #54dc9c;
            margin-bottom: 8px;
        }

        .upload-box p {
            font-size: 13px;
            font-weight: 600;
        }

        .upload-box small {
            display: block;
            margin-top: 7px;
            font-size: 11px;
            color: #aaa;
        }

        #preview-container {
            display: none;
            position: relative;
        }

        #preview {
            width: 100%;
            height: 145px;
            object-fit: cover;
            border-radius: 18px;
        }

        .remove-photo {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 50%;
            background: white;
            color: #b00000;
            font-weight: bold;
            cursor: pointer;
        }

        .save-button {
            display: block;
            margin: 25px auto 0;
            padding: 12px 35px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(90deg, #087b55, #32c34f);
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(30,190,90,.3);
        }

        .error {
            color: #ffaaaa;
            font-size: 12px;
            margin-top: 6px;
        }

        .validation-box {
            margin-bottom: 20px;
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(180, 30, 30, .35);
            color: #ffdada;
        }

        @media(max-width: 650px) {
            .form-container {
                padding: 25px 20px;
                border-radius: 25px;
            }

            .back-button {
                left: -8px;
                top: 15px;
                width: 40px;
            }

            h1 {
                font-size: 23px;
                padding-left: 20px;
            }

            .period-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .upload-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="form-container">

    <a href="{{ route('admin.laporan-kegiatan.index') }}"
       class="back-button"
       title="Kembali">↶</a>

    <h1>Tambah Laporan Mingguan</h1>

    <div class="line"></div>

    @if($errors->any())
        <div class="validation-box">
            <strong>Periksa kembali data yang kamu masukkan:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.laporan-kegiatan.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="period-row">

            <div class="form-group">
                <label for="bulan">Periode</label>

                <select name="bulan" id="bulan" required>
                    <option value="">Pilih Bulan</option>

                    @foreach(range(1, 12) as $bulan)
                        <option value="{{ $bulan }}"
                            @selected(old('bulan', now()->month) == $bulan)>
                            {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <input type="number"
                       name="tahun"
                       class="year-input"
                       min="2020"
                       max="2100"
                       value="{{ old('tahun', now()->year) }}"
                       placeholder="Tahun"
                       required>

                @error('bulan')
                    <div class="error">{{ $message }}</div>
                @enderror

                @error('tahun')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="minggu">Minggu</label>

                <select name="minggu" id="minggu" required>
                    <option value="">Pilih Minggu</option>

                    @foreach(range(1, 5) as $minggu)
                        <option value="{{ $minggu }}"
                            @selected(old('minggu') == $minggu)>
                            Minggu ke-{{ $minggu }}
                        </option>
                    @endforeach
                </select>

                @error('minggu')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal</label>

                <input type="date"
                       name="tanggal"
                       id="tanggal"
                       value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                       required>

                @error('tanggal')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="section-line"></div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Kegiatan</label>

            <textarea name="deskripsi"
                      id="deskripsi"
                      maxlength="500"
                      placeholder="Jelaskan kegiatan yang dilakukan selama periode ini..."
                      required>{{ old('deskripsi') }}</textarea>

            <div class="counter">
                <span id="char-count">0</span>/500
            </div>

            @error('deskripsi')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="section-line"></div>

        <div class="form-group">
            <label>Dokumentasi (Opsional)</label>

            <div class="upload-section">

                <label for="foto" class="upload-box">
                    <div>
                        <div class="upload-icon">☁</div>
                        <p>Klik untuk mengunggah foto</p>
                        <small>atau seret file ke sini</small>
                        <small>(JPG, PNG, maks. 5MB)</small>
                    </div>
                </label>

                <div class="upload-box" id="preview-container">
                    <img id="preview" alt="Preview foto">
                    <button type="button"
                            class="remove-photo"
                            id="remove-photo">×</button>
                </div>

            </div>

            <input type="file"
                   name="foto"
                   id="foto"
                   accept=".jpg,.jpeg,.png"
                   hidden>

            @error('foto')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="save-button">
            Simpan
        </button>

    </form>

</div>

<script>
    // Penghitung karakter deskripsi
    const deskripsi = document.getElementById('deskripsi');
    const charCount = document.getElementById('char-count');

    function updateCounter() {
        charCount.textContent = deskripsi.value.length;
    }

    deskripsi.addEventListener('input', updateCounter);
    updateCounter();

    // Preview foto dokumentasi
    const foto = document.getElementById('foto');
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('preview-container');
    const removePhoto = document.getElementById('remove-photo');

    foto.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Format foto harus JPG atau PNG.');
                this.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto maksimal 5 MB.');
                this.value = '';
                return;
            }

            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'flex';
        }
    });

    removePhoto.addEventListener('click', function () {
        foto.value = '';
        preview.src = '';
        previewContainer.style.display = 'none';
    });
</script>

</body>
</html>
