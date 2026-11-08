<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JenisSampah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JenisSampahTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_jenis_sampah(): void
    {
        $admin = User::create([
            'name' => 'Admin Pengujian',
            'username' => 'admin_test',
            'email' => 'admin_test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('admin.jenis-sampah.store'),
            [
                'nama' => 'Kardus',
                'harga_per_kg' => 1500,
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Kardus');

        $this->assertDatabaseHas('jenis_sampahs', [
            'nama' => 'Kardus',
            'aktif' => true,
        ]);
    }

    public function test_admin_can_update_jenis_sampah(): void
    {
        $admin = User::create([
            'name' => 'Admin Pengujian',
            'username' => 'admin_test',
            'email' => 'admin_test@example.com',
            'password' => 'password123',
        ]);

        $jenisSampah = JenisSampah::create([
            'nama' => 'Botol',
            'harga_per_kg' => 2000,
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->putJson(
            route('admin.jenis-sampah.update', $jenisSampah->id),
            [
                'nama' => 'Botol Plastik',
                'harga_per_kg' => 2500,
            ]
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Botol Plastik');

        $this->assertDatabaseHas('jenis_sampahs', [
            'id' => $jenisSampah->id,
            'nama' => 'Botol Plastik',
        ]);
    }

    
public function test_admin_can_deactivate_jenis_sampah(): void
{
    $admin = User::create([
        'name' => 'Admin Pengujian',
        'username' => 'admin_test',
        'email' => 'admin_test@example.com',
        'password' => 'password123',
    ]);

    $jenisSampah = JenisSampah::create([
        'nama' => 'Besi',
        'harga_per_kg' => 5000,
        'aktif' => true,
    ]);

    $response = $this->actingAs($admin)->patchJson(
        route('admin.jenis-sampah.status', $jenisSampah->id)
    );

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.aktif', false);

    $this->assertDatabaseHas('jenis_sampahs', [
        'id' => $jenisSampah->id,
        'aktif' => false,
    ]);
}


}
