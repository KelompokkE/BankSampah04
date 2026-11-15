<?php

namespace App\Http\Controllers;

use App\Models\LaporanKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanKegiatanController extends Controller
{
    // Menampilkan daftar laporan
    public function index(Request $request)
    {
        $query = LaporanKegiatan::query();

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter berdasarkan minggu
        if ($request->filled('minggu')) {
            $query->where('minggu', $request->minggu);
        }

        // Pencarian deskripsi laporan
        if ($request->filled('cari')) {
            $query->where('deskripsi', 'like', '%' . $request->cari . '%');
        }

        $laporans = $query
            ->orderByDesc('tanggal')
            ->paginate(8)
            ->withQueryString();

        return view('admin.laporan-kegiatan.index', compact('laporans'));
    }

    // Menampilkan formulir tambah laporan
    public function create()
    {
        return view('admin.laporan-kegiatan.create');
    }

    // Menyimpan laporan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'minggu' => [
                'required',
                'integer',
                'between:1,5',
                Rule::unique('laporan_kegiatans')
                    ->where('bulan', $request->bulan)
                    ->where('tahun', $request->tahun),
            ],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        // Upload foto jika tersedia
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')
                ->store('laporan-kegiatan', 'public');
        }

        LaporanKegiatan::create($validated);

        return redirect()
            ->route('admin.laporan-kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil ditambahkan.');
    }

    // Menampilkan detail laporan
    public function show(string $id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);

        return view('admin.laporan-kegiatan.show', compact('laporan'));
    }

    // Menampilkan formulir edit
    public function edit(string $id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);

        return view('admin.laporan-kegiatan.edit', compact('laporan'));
    }

    // Memperbarui laporan
    public function update(Request $request, string $id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);

        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'minggu' => [
                'required',
                'integer',
                'between:1,5',
                Rule::unique('laporan_kegiatans')
                    ->where('bulan', $request->bulan)
                    ->where('tahun', $request->tahun)
                    ->ignore($laporan->id),
            ],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        // Ganti foto jika ada foto baru
        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }

            $validated['foto'] = $request->file('foto')
                ->store('laporan-kegiatan', 'public');
        }

        $laporan->update($validated);

        return redirect()
            ->route('admin.laporan-kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    // Menghapus laporan
    public function destroy(string $id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);

        // Hapus foto dari storage
        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return redirect()
            ->route('admin.laporan-kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil dihapus.');
    }
}
