<?php

namespace App\Http\Controllers;

use App\Models\Pengumpulan;
use App\Models\Nasabah;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumpulanController extends Controller
{
    // Menampilkan seluruh riwayat pengumpulan
    public function index()
    {
        $pengumpulan = Pengumpulan::with([
            'nasabah',
            'detailPengumpulans.jenisSampah'
        ])
        ->orderByDesc('tanggal_pengumpulan')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumpulan berhasil diambil.',
            'data' => $pengumpulan,
        ]);
    }

    // Menambahkan data pengumpulan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nasabah_id' => ['required', 'exists:nasabahs,id'],
            'tanggal_pengumpulan' => ['required', 'date'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.jenis_sampah_id' => [
                'required',
                'exists:jenis_sampahs,id',
                'distinct',
            ],
            'detail.*.berat' => ['required', 'numeric', 'gt:0'],
        ]);

        $pengumpulan = DB::transaction(function () use ($validated) {
            $pengumpulan = Pengumpulan::create([
                'nasabah_id' => $validated['nasabah_id'],
                'tanggal_pengumpulan' => $validated['tanggal_pengumpulan'],
            ]);

            foreach ($validated['detail'] as $item) {
                $pengumpulan->detailPengumpulans()->create([
                    'jenis_sampah_id' => $item['jenis_sampah_id'],
                    'berat' => $item['berat'],
                ]);
            }

            return $pengumpulan;
        });

        $pengumpulan->load([
            'nasabah',
            'detailPengumpulans.jenisSampah'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengumpulan berhasil ditambahkan.',
            'data' => $pengumpulan,
        ], 201);
    }

    
    // Menampilkan riwayat pengumpulan berdasarkan nasabah
    public function riwayatNasabah(Nasabah $nasabah)
    {
        $pengumpulan = $nasabah->pengumpulans()
            ->with('detailPengumpulans.jenisSampah')
            ->orderByDesc('tanggal_pengumpulan')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengumpulan nasabah berhasil diambil.',
            'data' => [
                'nasabah' => $nasabah,
                'riwayat' => $pengumpulan,
            ],
        ]);
    }


}
