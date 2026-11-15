<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Nasabah;
use App\Models\JenisSampah;
use App\Models\Pengumpulan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengumpulanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Pengujian',
            'username' => 'admin_test',
            'email' => 'admin_test@example.com',
            'password' => 'password123',
        ]);
    }

    private function nasabah(): Nasabah
    {
        return Nasabah::create([
            'kode_nasabah' => 'NSB-001',
            'nama' => 'Budi Santoso',
            'alamat' => 'Jl. Cikokol No. 10',
            'rt' => '01',
        ]);
    }

    private function jenisSampah(): JenisSampah
    {
        return JenisSampah::create([
            'nama' => 'Kardus',
            'harga_per_kg' => 1500,
            'aktif' => true,
        ]);
    }

    public function test_admin_can_view_pengumpulan(): void
    {
        $admin = $this->admin();
        $nasabah = $this->nasabah();

        Pengumpulan::create([
            'nasabah_id' => $nasabah->id,
            'tanggal_pengumpulan' => '2026-11-15',
        ]);

        $response = $this->actingAs($admin)->getJson(
            route('admin.pengumpulan.index')
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.nasabah.nama', 'Budi Santoso');
    }

    public function test_admin_can_add_pengumpulan_with_multiple_waste_types(): void
    {
        $admin = $this->admin();
        $nasabah = $this->nasabah();

        $kardus = $this->jenisSampah();

        $botol = JenisSampah::create([
            'nama' => 'Botol',
            'harga_per_kg' => 2000,
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('admin.pengumpulan.store'),
            [
                'nasabah_id' => $nasabah->id,
                'tanggal_pengumpulan' => '2026-11-15',
                'detail' => [
                    [
                        'jenis_sampah_id' => $kardus->id,
                        'berat' => 5,
                    ],
                    [
                        'jenis_sampah_id' => $botol->id,
                        'berat' => 3,
                    ],
                ],
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nasabah.nama', 'Budi Santoso')
            ->assertJsonCount(2, 'data.detail_pengumpulans');

       $this->assertDatabaseHas('pengumpulans', [
    'nasabah_id' => $nasabah->id,
    'tanggal_pengumpulan' => '2026-11-15 00:00:00',
]);
        $this->assertDatabaseHas('detail_pengumpulans', [
            'jenis_sampah_id' => $kardus->id,
            'berat' => 5,
        ]);

        $this->assertDatabaseHas('detail_pengumpulans', [
            'jenis_sampah_id' => $botol->id,
            'berat' => 3,
        ]);
    }

    public function test_pengumpulan_requires_at_least_one_detail(): void
    {
        $admin = $this->admin();
        $nasabah = $this->nasabah();

        $response = $this->actingAs($admin)->postJson(
            route('admin.pengumpulan.store'),
            [
                'nasabah_id' => $nasabah->id,
                'tanggal_pengumpulan' => '2026-11-15',
                'detail' => [],
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['detail']);
    }

    
    public function test_admin_can_view_nasabah_collection_history(): void
    {
        $admin = $this->admin();
        $nasabah = $this->nasabah();

        $pengumpulan = Pengumpulan::create([
            'nasabah_id' => $nasabah->id,
            'tanggal_pengumpulan' => '2026-11-15',
        ]);

        $jenisSampah = $this->jenisSampah();

        $pengumpulan->detailPengumpulans()->create([
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 5,
        ]);

        $response = $this->actingAs($admin)->getJson(
            route('admin.nasabah.riwayat', $nasabah->id)
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nasabah.nama', 'Budi Santoso')
            ->assertJsonCount(1, 'data.riwayat')
            ->assertJsonPath(
                'data.riwayat.0.detail_pengumpulans.0.berat',
                '5.00'
            );
    }


}
