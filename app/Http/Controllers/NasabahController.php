<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    // Menampilkan seluruh data nasabah
    public function index()
    {
        $nasabah = Nasabah::orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data nasabah berhasil diambil.',
            'data' => $nasabah,
        ]);
    }

    // Menambahkan data nasabah
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_nasabah' => ['required', 'string', 'max:255', 'unique:nasabahs,kode_nasabah'],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'rt' => ['required', 'in:01,02,03,04'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $nasabah = Nasabah::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data nasabah berhasil ditambahkan.',
            'data' => $nasabah,
        ], 201);
    }

    
    // Mengubah data nasabah
    public function update(Request $request, Nasabah $nasabah)
    {
        $validated = $request->validate([
            'kode_nasabah' => [
                'required',
                'string',
                'max:255',
                'unique:nasabahs,kode_nasabah,' . $nasabah->id,
            ],
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'rt' => ['required', 'in:01,02,03,04'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $nasabah->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data nasabah berhasil diperbarui.',
            'data' => $nasabah,
        ]);
    }

    // Menghapus data nasabah
public function destroy(Nasabah $nasabah)
{
    // Periksa apakah nasabah memiliki riwayat pengumpulan
    if ($nasabah->pengumpulans()->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Nasabah tidak dapat dihapus karena sudah memiliki riwayat pengumpulan.',
        ], 422);
    }

    $nasabah->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data nasabah berhasil dihapus.',
    ]);
}


}
