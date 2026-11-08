<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\Penimbangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenimbanganController extends Controller
{
    // Menampilkan seluruh riwayat penimbangan
    public function index()
    {
        $penimbangan = Penimbangan::with('jenisSampah')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data penimbangan berhasil diambil.',
            'data' => $penimbangan,
        ]);
    }

    // Menambahkan data penimbangan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis_sampah_id' => [
                'required',
                'integer',
                'exists:jenis_sampahs,id',
            ],
            'berat' => ['required', 'numeric', 'gt:0'],
        ]);

        $penimbangan = DB::transaction(function () use ($validated) {
            $jenisSampah = JenisSampah::where('aktif', true)
    ->whereKey($validated['jenis_sampah_id'])
    ->lockForUpdate()
    ->first();

if (!$jenisSampah) {
    throw ValidationException::withMessages([
        'jenis_sampah_id' => 'Jenis sampah sudah dinonaktifkan atau tidak tersedia.',
    ]);
}

            $harga = $jenisSampah->harga_per_kg;
            $total = (float) $validated['berat'] * (float) $harga;

            return Penimbangan::create([
                'tanggal' => $validated['tanggal'],
                'jenis_sampah_id' => $jenisSampah->id,
                'berat' => $validated['berat'],
                'harga_per_kg' => $harga,
                'total' => $total,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data penimbangan berhasil ditambahkan.',
            'data' => $penimbangan->load('jenisSampah'),
        ], 201);
    }

    // Mengubah data penimbangan
    public function update(Request $request, Penimbangan $penimbangan)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis_sampah_id' => [
                'required',
                'integer',
                'exists:jenis_sampahs,id',
            ],
            'berat' => ['required', 'numeric', 'gt:0'],
        ]);

        $penimbangan = DB::transaction(function () use ($validated, $penimbangan) {
            $jenisSampah = JenisSampah::whereKey($validated['jenis_sampah_id'])
    ->lockForUpdate()
    ->first();

if (!$jenisSampah) {
    throw ValidationException::withMessages([
        'jenis_sampah_id' => 'Jenis sampah tidak ditemukan.',
    ]);
}

// Jika jenis sampah diganti, jenis baru harus aktif
if (
    (int) $penimbangan->jenis_sampah_id !== (int) $jenisSampah->id
    && !$jenisSampah->aktif
) {
    throw ValidationException::withMessages([
        'jenis_sampah_id' => 'Jenis sampah yang dipilih sudah dinonaktifkan.',
    ]);
}

            $penimbangan->tanggal = $validated['tanggal'];
            $penimbangan->jenis_sampah_id = $jenisSampah->id;
            $penimbangan->berat = $validated['berat'];

            // Gunakan harga lama jika jenis sampah tidak berubah
            // Jika jenis berubah, gunakan harga terbaru
            if ($penimbangan->isDirty('jenis_sampah_id')) {
                $penimbangan->harga_per_kg = $jenisSampah->harga_per_kg;
            }

            $penimbangan->total =
                (float) $penimbangan->berat *
                (float) $penimbangan->harga_per_kg;

            $penimbangan->save();

            return $penimbangan;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data penimbangan berhasil diperbarui.',
            'data' => $penimbangan->load('jenisSampah'),
        ]);
    }

    // Menghapus data penimbangan
    public function destroy(Penimbangan $penimbangan)
    {
        $penimbangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penimbangan berhasil dihapus.',
        ]);
    }
}
