
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Uji Coba Penimbangan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 25px;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }

        h2 { margin-top: 0; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 15px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 12px;
        }

        .btn-save { background: #198754; color: white; }
        .btn-edit { background: #ffc107; color: #222; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-cancel { background: #6c757d; color: white; }

        .total {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th { background: #f0f2f5; }

        #pesan {
            margin-bottom: 15px;
            font-weight: bold;
        }

        @media (max-width: 650px) {
            body { padding: 12px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Uji Coba Penimbangan Bank Sampah</h1>
    <p>Halaman sementara untuk menguji fungsi backend Penimbangan.</p>

    <div id="pesan"></div>

    <div class="card">
        <h2 id="judul-form">Tambah Penimbangan</h2>

        <form id="formPenimbangan">
            <div class="form-grid">
                <div>
                    <label for="tanggal">Tanggal Penimbangan</label>
                    <input type="date" id="tanggal" required>
                </div>

                <div>
                    <label for="jenis_sampah_id">Jenis Sampah</label>
                    <select id="jenis_sampah_id" required>
                        <option value="">Memuat jenis sampah...</option>
                    </select>
                </div>

                <div>
                    <label for="berat">Berat (Kg)</label>
                    <input type="number" id="berat" min="0.01"
                           step="0.01" placeholder="Contoh: 5.5" required>
                </div>

                <div>
                    <label>Harga per Kg</label>
                    <input type="text" id="harga" readonly
                           placeholder="Otomatis">
                </div>
            </div>

            <div class="total">
                <strong>Perkiraan Total:</strong>
                <h2 id="total">Rp0</h2>
            </div>

            <button type="submit" class="btn-save" id="tombol-simpan">
                Simpan Penimbangan
            </button>

            <button type="button" class="btn-cancel"
                    id="tombol-batal" hidden>
                Batal Edit
            </button>
        </form>
    </div>

    <div class="card">
        <h2>Riwayat Penimbangan</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Sampah</th>
                        <th>Berat</th>
                        <th>Harga/Kg</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="daftar-penimbangan">
                    <tr>
                        <td colspan="7">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    ).content;

    const urlPenimbangan = '/admin/penimbangan';
    const urlJenisSampah = '/admin/jenis-sampah';

    let daftarJenis = [];
    let daftarPenimbangan = [];
    let idEdit = null;

    const form = document.getElementById('formPenimbangan');
    const tanggal = document.getElementById('tanggal');
    const jenisInput = document.getElementById('jenis_sampah_id');
    const beratInput = document.getElementById('berat');
    const hargaInput = document.getElementById('harga');
    const totalText = document.getElementById('total');
    const pesan = document.getElementById('pesan');

    function rupiah(nilai) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 2
        }).format(Number(nilai) || 0);
    }

    function tampilPesan(teks, error = false) {
        pesan.textContent = teks;
        pesan.style.color = error ? '#dc3545' : '#198754';
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...(options.headers || {})
            }
        });

        const hasil = await response.json();

        if (!response.ok) {
            const detail = hasil.errors
                ? Object.values(hasil.errors).flat().join(' ')
                : hasil.message;

            throw new Error(detail || 'Permintaan gagal.');
        }

        return hasil;
    }

    async function muatJenisSampah() {
        const hasil = await request(urlJenisSampah);

        daftarJenis = hasil.data.filter(item => item.aktif);

        jenisInput.innerHTML =
            '<option value="">Pilih jenis sampah</option>';

        daftarJenis.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nama;
            jenisInput.appendChild(option);
        });
    }

    function hitungTotal() {
        const jenis = daftarJenis.find(
            item => Number(item.id) === Number(jenisInput.value)
        );

        if (!jenis) {
            hargaInput.value = '';
            totalText.textContent = rupiah(0);
            return;
        }

        const harga = Number(jenis.harga_per_kg);
        const berat = Number(beratInput.value) || 0;

        hargaInput.value = rupiah(harga);
        totalText.textContent = rupiah(harga * berat);
    }

    async function muatPenimbangan() {
        const hasil = await request(urlPenimbangan);
        daftarPenimbangan = hasil.data;
        tampilkanTabel();
    }

    function tampilkanTabel() {
        const tbody = document.getElementById('daftar-penimbangan');
        tbody.innerHTML = '';

        if (daftarPenimbangan.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">Belum ada data penimbangan.</td>
                </tr>
            `;
            return;
        }

        daftarPenimbangan.forEach((item, index) => {
            const tr = document.createElement('tr');

            const tanggalTampil = String(item.tanggal).substring(0, 10);
            const namaJenis = item.jenis_sampah?.nama || '-';

            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${tanggalTampil}</td>
                <td></td>
                <td>${Number(item.berat).toLocaleString('id-ID')} Kg</td>
                <td>${rupiah(item.harga_per_kg)}</td>
                <td>${rupiah(item.total)}</td>
                <td>
                    <button class="btn-edit" data-action="edit">
                        Edit
                    </button>
                    <button class="btn-delete" data-action="hapus">
                        Hapus
                    </button>
                </td>
            `;

            // Tampilkan nama jenis sebagai teks, bukan HTML dari server.
            tr.children[2].textContent = namaJenis;

            tr.querySelector('[data-action="edit"]').addEventListener(
                'click', () => mulaiEdit(item)
            );

            tr.querySelector('[data-action="hapus"]').addEventListener(
                'click', () => hapusData(item.id)
            );

            tbody.appendChild(tr);
        });
    }

    function mulaiEdit(item) {
        idEdit = item.id;

        tanggal.value = String(item.tanggal).substring(0, 10);
        jenisInput.value = item.jenis_sampah_id;
        beratInput.value = item.berat;

        hitungTotal();

        document.getElementById('judul-form').textContent =
            'Edit Penimbangan';

        document.getElementById('tombol-simpan').textContent =
            'Simpan Perubahan';

        document.getElementById('tombol-batal').hidden = false;

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        idEdit = null;
        form.reset();
        hargaInput.value = '';
        totalText.textContent = rupiah(0);

        document.getElementById('judul-form').textContent =
            'Tambah Penimbangan';

        document.getElementById('tombol-simpan').textContent =
            'Simpan Penimbangan';

        document.getElementById('tombol-batal').hidden = true;
    }

    async function hapusData(id) {
        if (!confirm('Yakin ingin menghapus data penimbangan ini?')) {
            return;
        }

        try {
            await request(`${urlPenimbangan}/${id}`, {
                method: 'DELETE'
            });

            tampilPesan('Data berhasil dihapus.');
            await muatPenimbangan();
        } catch (error) {
            tampilPesan(error.message, true);
        }
    }

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const data = {
            tanggal: tanggal.value,
            jenis_sampah_id: Number(jenisInput.value),
            berat: Number(beratInput.value)
        };

        try {
            if (idEdit) {
                await request(`${urlPenimbangan}/${idEdit}`, {
                    method: 'PUT',
                    body: JSON.stringify(data)
                });

                tampilPesan('Data penimbangan berhasil diperbarui.');
            } else {
                await request(urlPenimbangan, {
                    method: 'POST',
                    body: JSON.stringify(data)
                });

                tampilPesan('Data penimbangan berhasil ditambahkan.');
            }

            resetForm();
            await muatPenimbangan();

        } catch (error) {
            tampilPesan(error.message, true);
        }
    });

    jenisInput.addEventListener('change', hitungTotal);
    beratInput.addEventListener('input', hitungTotal);

    document.getElementById('tombol-batal').addEventListener(
        'click', resetForm
    );

    async function mulaiAplikasi() {
        try {
            await muatJenisSampah();
            await muatPenimbangan();
        } catch (error) {
            tampilPesan(error.message, true);
        }
    }

    mulaiAplikasi();
</script>

</body>
</html>
