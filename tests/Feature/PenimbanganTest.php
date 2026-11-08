<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenimbanganTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_penimbangan_with_automatic_total(): void
    {
        // Membuat akun admin untuk pengujian
        $admin = User::factory()->create();

        // Membuat jenis sampah dengan harga Rp2.000/kg
        $jenisSampah = JenisSampah::create([
            'nama' => 'Kardus',
            'harga_per_kg' => 2000,
            'aktif' => true,
        ]);

        // Admin menambahkan penimbangan 5 kg
        $response = $this->actingAs($admin)->postJson(
            route('admin.penimbangan.store'),
            [
                'tanggal' => '2026-11-08',
                'jenis_sampah_id' => $jenisSampah->id,
                'berat' => 5,
            ]
        );

        // Memastikan data berhasil ditambahkan
        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.berat', '5.00')
            ->assertJsonPath('data.harga_per_kg', '2000.00')
            ->assertJsonPath('data.total', '10000.00');

        // Memastikan data benar-benar tersimpan di database
        $this->assertDatabaseHas('penimbangans', [
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 5,
            'harga_per_kg' => 2000,
            'total' => 10000,
        ]);
    }

    
public function test_old_penimbangan_keeps_original_price_when_current_price_changes(): void
{
    $admin = User::factory()->create();

    // Harga awal Kardus Rp2.000/kg
    $jenisSampah = JenisSampah::create([
        'nama' => 'Kardus',
        'harga_per_kg' => 2000,
        'aktif' => true,
    ]);

    // Menimbang Kardus sebanyak 5 kg
    $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 5,
        ]
    )->assertCreated();

    // Admin mengubah harga menjadi Rp3.000/kg
    $jenisSampah->update([
        'harga_per_kg' => 3000,
    ]);

    // Riwayat penimbangan harus tetap memakai harga lama
    $this->assertDatabaseHas('penimbangans', [
        'jenis_sampah_id' => $jenisSampah->id,
        'berat' => 5,
        'harga_per_kg' => 2000,
        'total' => 10000,
    ]);
}

    
public function test_admin_can_update_penimbangan_and_recalculate_total(): void
{
    $admin = User::factory()->create();

    $jenisSampah = JenisSampah::create([
        'nama' => 'Botol Plastik',
        'harga_per_kg' => 3000,
        'aktif' => true,
    ]);

    // Membuat data penimbangan awal
    $response = $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 5,
        ]
    );

    $response->assertCreated();

    $penimbanganId = $response->json('data.id');

    // Mengubah berat menjadi 10 kg
    $updateResponse = $this->putJson(
        route('admin.penimbangan.update', $penimbanganId),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 10,
        ]
    );

    $updateResponse->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.berat', '10.00')
        ->assertJsonPath('data.harga_per_kg', '3000.00')
        ->assertJsonPath('data.total', '30000.00');

    // Memastikan perubahan tersimpan
    $this->assertDatabaseHas('penimbangans', [
        'id' => $penimbanganId,
        'berat' => 10,
        'harga_per_kg' => 3000,
        'total' => 30000,
    ]);
}

    
public function test_admin_can_delete_penimbangan(): void
{
    $admin = User::factory()->create();

    $jenisSampah = JenisSampah::create([
        'nama' => 'Besi',
        'harga_per_kg' => 5000,
        'aktif' => true,
    ]);

    // Membuat data penimbangan
    $response = $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 4,
        ]
    );

    $response->assertCreated();

    $penimbanganId = $response->json('data.id');

    // Menghapus data penimbangan
    $deleteResponse = $this->deleteJson(
        route('admin.penimbangan.destroy', $penimbanganId)
    );

    $deleteResponse->assertOk()
        ->assertJsonPath('success', true);

    // Memastikan data sudah tidak ada
    $this->assertDatabaseMissing('penimbangans', [
        'id' => $penimbanganId,
    ]);
}

    
public function test_guest_cannot_access_penimbangan_data(): void
{
    // Pengguna yang belum login mencoba membuka data penimbangan
    $response = $this->getJson(
        route('admin.penimbangan.index')
    );

    // Akses harus ditolak
    $response->assertUnauthorized();
}

    
public function test_penimbangan_rejects_zero_or_negative_weight(): void
{
    $admin = User::factory()->create();

    $jenisSampah = JenisSampah::create([
        'nama' => 'Kardus',
        'harga_per_kg' => 2000,
        'aktif' => true,
    ]);

    // Menguji berat nol
    $responseNol = $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 0,
        ]
    );

    $responseNol->assertUnprocessable()
        ->assertJsonValidationErrors(['berat']);

    // Menguji berat negatif
    $responseNegatif = $this->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => -5,
        ]
    );

    $responseNegatif->assertUnprocessable()
        ->assertJsonValidationErrors(['berat']);

    // Tidak boleh ada data yang tersimpan
    $this->assertDatabaseCount('penimbangans', 0);
}

    
public function test_admin_cannot_add_penimbangan_using_inactive_jenis_sampah(): void
{
    $admin = User::factory()->create();

    // Membuat jenis sampah yang sudah dinonaktifkan
    $jenisSampah = JenisSampah::create([
        'nama' => 'Botol Kaca',
        'harga_per_kg' => 1500,
        'aktif' => false,
    ]);

    // Admin mencoba menggunakan jenis sampah nonaktif
    $response = $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisSampah->id,
            'berat' => 5,
        ]
    );

    // Sistem harus menolak permintaan
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['jenis_sampah_id']);

    // Data penimbangan tidak boleh tersimpan
    $this->assertDatabaseCount('penimbangans', 0);
}

    
public function test_admin_cannot_change_penimbangan_to_inactive_jenis_sampah(): void
{
    $admin = User::factory()->create();

    $jenisAktif = JenisSampah::create([
        'nama' => 'Kardus',
        'harga_per_kg' => 2000,
        'aktif' => true,
    ]);

    $jenisNonaktif = JenisSampah::create([
        'nama' => 'Botol Kaca',
        'harga_per_kg' => 1500,
        'aktif' => false,
    ]);

    // Membuat penimbangan menggunakan jenis aktif
    $response = $this->actingAs($admin)->postJson(
        route('admin.penimbangan.store'),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisAktif->id,
            'berat' => 5,
        ]
    );

    $response->assertCreated();

    $penimbanganId = $response->json('data.id');

    // Mencoba mengganti ke jenis sampah nonaktif
    $updateResponse = $this->putJson(
        route('admin.penimbangan.update', $penimbanganId),
        [
            'tanggal' => '2026-11-08',
            'jenis_sampah_id' => $jenisNonaktif->id,
            'berat' => 10,
        ]
    );

    $updateResponse->assertUnprocessable()
        ->assertJsonValidationErrors(['jenis_sampah_id']);

    // Data lama harus tetap tidak berubah
    $this->assertDatabaseHas('penimbangans', [
        'id' => $penimbanganId,
        'jenis_sampah_id' => $jenisAktif->id,
        'berat' => 5,
        'harga_per_kg' => 2000,
        'total' => 10000,
    ]);
}


}
