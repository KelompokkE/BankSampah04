<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pengumpulan;

class NasabahTest extends TestCase
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

    public function test_admin_can_view_nasabah(): void
    {
        $admin = $this->admin();

        Nasabah::create([
            'kode_nasabah' => 'NSB-001',
            'nama' => 'Budi Santoso',
            'alamat' => 'Jl. Cikokol No. 10',
            'rt' => '01',
            'no_telepon' => '081234567890',
        ]);

        $response = $this->actingAs($admin)->getJson(
            route('admin.nasabah.index')
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.nama', 'Budi Santoso');
    }

    public function test_admin_can_add_nasabah(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(
            route('admin.nasabah.store'),
            [
                'kode_nasabah' => 'NSB-002',
                'nama' => 'Siti Aminah',
                'alamat' => 'Jl. Cikokol No. 12',
                'rt' => '02',
                'no_telepon' => '081234567891',
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Siti Aminah');

        $this->assertDatabaseHas('nasabahs', [
            'kode_nasabah' => 'NSB-002',
            'nama' => 'Siti Aminah',
            'rt' => '02',
        ]);
    }

    public function test_admin_can_update_nasabah(): void
    {
        $admin = $this->admin();

        $nasabah = Nasabah::create([
            'kode_nasabah' => 'NSB-003',
            'nama' => 'Budi Santoso',
            'alamat' => 'Jl. Cikokol No. 10',
            'rt' => '01',
        ]);

        $response = $this->actingAs($admin)->putJson(
            route('admin.nasabah.update', $nasabah->id),
            [
                'kode_nasabah' => 'NSB-003',
                'nama' => 'Budi Santoso Baru',
                'alamat' => 'Jl. Cikokol No. 15',
                'rt' => '03',
                'no_telepon' => '081234567890',
            ]
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Budi Santoso Baru');

        $this->assertDatabaseHas('nasabahs', [
            'id' => $nasabah->id,
            'nama' => 'Budi Santoso Baru',
            'rt' => '03',
        ]);
    }

    public function test_admin_can_delete_nasabah(): void
    {
        $admin = $this->admin();

        $nasabah = Nasabah::create([
            'kode_nasabah' => 'NSB-004',
            'nama' => 'Andi',
            'alamat' => 'Jl. Cikokol No. 20',
            'rt' => '04',
        ]);

        $response = $this->actingAs($admin)->deleteJson(
            route('admin.nasabah.destroy', $nasabah->id)
        );

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('nasabahs', [
            'id' => $nasabah->id,
        ]);
    }

    public function test_nasabah_rt_must_be_valid(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson(
            route('admin.nasabah.store'),
            [
                'kode_nasabah' => 'NSB-005',
                'nama' => 'Dewi',
                'alamat' => 'Jl. Cikokol No. 25',
                'rt' => '05',
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['rt']);
    }

    public function test_admin_cannot_delete_nasabah_with_collection_history(): void
{
    $admin = $this->admin();

    $nasabah = Nasabah::create([
        'kode_nasabah' => 'NSB-TEST',
        'nama' => 'Budi Santoso',
        'alamat' => 'Cikokol',
        'rt' => '01',
        'no_telepon' => '08123456789',
    ]);

    Pengumpulan::create([
        'nasabah_id' => $nasabah->id,
        'tanggal_pengumpulan' => '2026-11-15',
    ]);

    $response = $this->actingAs($admin)
        ->deleteJson(route('admin.nasabah.destroy', $nasabah));

    $response->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'message' => 'Nasabah tidak dapat dihapus karena sudah memiliki riwayat pengumpulan.',
        ]);

    $this->assertDatabaseHas('nasabahs', [
        'id' => $nasabah->id,
    ]);

    $this->assertDatabaseHas('pengumpulans', [
        'nasabah_id' => $nasabah->id,
    ]);
}

}
