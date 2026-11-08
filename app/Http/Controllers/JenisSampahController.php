<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use Illuminate\Http\Request;

class JenisSampahController extends Controller
{
    // Menampilkan seluruh jenis sampah
    public function index()
    {
        $jenisSampah = JenisSampah::orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data jenis sampah berhasil diambil.',
            'data' => $jenisSampah,
        ]);
    }

    // Menambahkan jenis sampah
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jenis_sampahs,nama'],
            'harga_per_kg' => ['required', 'numeric', 'min:0'],
        ]);

        $jenisSampah = JenisSampah::create([
            'nama' => $validated['nama'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'aktif' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jenis sampah berhasil ditambahkan.',
            'data' => $jenisSampah,
        ], 201);
    }

    // Mengubah jenis sampah
    public function update(Request $request, JenisSampah $jenisSampah)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:jenis_sampahs,nama,' . $jenisSampah->id,
            ],
            'harga_per_kg' => ['required', 'numeric', 'min:0'],
        ]);

        $jenisSampah->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jenis sampah berhasil diperbarui.',
            'data' => $jenisSampah,
        ]);
    }

    // Mengaktifkan atau menonaktifkan jenis sampah
    public function toggleStatus(JenisSampah $jenisSampah)
    {
        $jenisSampah->update([
            'aktif' => !$jenisSampah->aktif,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status jenis sampah berhasil diperbarui.',
            'data' => $jenisSampah,
        ]);
    }
}
