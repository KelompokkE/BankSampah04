<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LaporanKegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanKegiatanTest extends TestCase
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

    private function dataLaporan(): array
    {
        return [
            'bulan' => 11,
            'tahun' => 2026,
            'minggu' => 1,
            'tanggal' => '2026-11-10',
            'deskripsi' => 'Kegiatan pembersihan dan pengelolaan sampah bersama warga.',
        ];
    }

    public function test_admin_can_view_laporan(): void
    {
        $admin = $this->admin();

        LaporanKegiatan::create($this->dataLaporan());

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan-kegiatan.index'));

        $response->assertOk()
            ->assertViewIs('admin.laporan-kegiatan.index')
            ->assertViewHas('laporans');
    }

    public function test_admin_can_add_laporan_with_photo(): void
    {
        Storage::fake('public');

        $admin = $this->admin();

        $data = $this->dataLaporan();
       $data['foto'] = UploadedFile::fake()->create(
    'kegiatan.jpg',
    100,
    'image/jpeg'
);

        $response = $this->actingAs($admin)
            ->post(route('admin.laporan-kegiatan.store'), $data);

        $response->assertRedirect(route('admin.laporan-kegiatan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('laporan_kegiatans', [
            'bulan' => 11,
            'tahun' => 2026,
            'minggu' => 1,
            'deskripsi' => $data['deskripsi'],
        ]);

        $laporan = LaporanKegiatan::first();

        $this->assertNotNull($laporan->foto);
        Storage::disk('public')->assertExists($laporan->foto);
    }

    public function test_admin_cannot_add_duplicate_weekly_report(): void
    {
        $admin = $this->admin();

        LaporanKegiatan::create($this->dataLaporan());

        $data = $this->dataLaporan();
        $data['tanggal'] = '2026-11-11';

        $response = $this->actingAs($admin)
            ->from(route('admin.laporan-kegiatan.create'))
            ->post(route('admin.laporan-kegiatan.store'), $data);

        $response->assertRedirect(route('admin.laporan-kegiatan.create'))
            ->assertSessionHasErrors(['minggu']);

        $this->assertDatabaseCount('laporan_kegiatans', 1);
    }

    public function test_admin_can_view_laporan_detail(): void
    {
        $admin = $this->admin();

        $laporan = LaporanKegiatan::create($this->dataLaporan());

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan-kegiatan.show', $laporan->id));

        $response->assertOk()
            ->assertViewIs('admin.laporan-kegiatan.show')
            ->assertViewHas('laporan');
    }

    public function test_admin_can_update_laporan_and_replace_photo(): void
    {
        Storage::fake('public');

        $admin = $this->admin();

        Storage::disk('public')->put(
            'laporan-kegiatan/foto-lama.jpg',
            'foto lama'
        );

        $laporan = LaporanKegiatan::create(array_merge(
            $this->dataLaporan(),
            ['foto' => 'laporan-kegiatan/foto-lama.jpg']
        ));

        $data = $this->dataLaporan();
        $data['deskripsi'] = 'Kegiatan kerja bakti bersama warga.';
        $data['foto'] = UploadedFile::fake()->create(
    'foto-baru.jpg',
    100,
    'image/jpeg'
);

        $response = $this->actingAs($admin)
            ->put(
                route('admin.laporan-kegiatan.update', $laporan->id),
                $data
            );

        $response->assertRedirect(route('admin.laporan-kegiatan.index'))
            ->assertSessionHas('success');

        $laporan->refresh();

        $this->assertSame(
            'Kegiatan kerja bakti bersama warga.',
            $laporan->deskripsi
        );

        Storage::disk('public')->assertMissing(
            'laporan-kegiatan/foto-lama.jpg'
        );

        Storage::disk('public')->assertExists($laporan->foto);
    }

    public function test_admin_can_delete_laporan_and_its_photo(): void
    {
        Storage::fake('public');

        $admin = $this->admin();

        Storage::disk('public')->put(
            'laporan-kegiatan/foto-hapus.jpg',
            'foto'
        );

        $laporan = LaporanKegiatan::create(array_merge(
            $this->dataLaporan(),
            ['foto' => 'laporan-kegiatan/foto-hapus.jpg']
        ));

        $response = $this->actingAs($admin)
            ->delete(
                route('admin.laporan-kegiatan.destroy', $laporan->id)
            );

        $response->assertRedirect(route('admin.laporan-kegiatan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('laporan_kegiatans', [
            'id' => $laporan->id,
        ]);

        Storage::disk('public')->assertMissing(
            'laporan-kegiatan/foto-hapus.jpg'
        );
    }

    public function test_guest_cannot_access_laporan(): void
    {
        $response = $this->get(route('admin.laporan-kegiatan.index'));

        $response->assertRedirect(route('login'));
    }
}
