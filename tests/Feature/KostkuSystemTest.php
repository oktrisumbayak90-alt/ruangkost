<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\TenantStay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KostkuSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $tenant;
    protected Room $room;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'nama'                 => 'Pemilik Test',
            'email'                => 'owner_test_' . rand(1000, 9999) . '@kostku.local',
            'password'             => bcrypt('password123'),
            'peran'                => 'pemilik',
            'is_aktif'             => true,
            'wajib_ganti_password' => false,
        ]);

        $this->tenant = User::create([
            'nama'                 => 'Penghuni Test',
            'email'                => 'tenant_test_' . rand(1000, 9999) . '@kostku.local',
            'password'             => bcrypt('password123'),
            'peran'                => 'penghuni',
            'is_aktif'             => true,
            'wajib_ganti_password' => false,
        ]);

        $this->room = Room::create([
            'nomor_kamar' => 'TEST-' . rand(100, 999),
            'lantai'      => '1',
            'kapasitas'   => 2,
            'status'      => 'tersedia',
        ]);

        $this->category = Category::create([
            'nama'     => 'Kebersihan',
            'slug'     => 'kebersihan-' . rand(1000, 9999),
            'ikon'     => 'bi-trash',
            'warna'    => '#10b981',
            'is_aktif' => true,
        ]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('KostKu');
    }

    public function test_guests_cannot_access_protected_routes(): void
    {
        $this->get('/owner/dashboard')->assertRedirect('/login');
        $this->get('/penghuni/dashboard')->assertRedirect('/login');
    }

    public function test_owner_can_access_owner_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/dashboard');
        $response->assertStatus(200);
    }

    public function test_tenant_cannot_access_owner_routes(): void
    {
        $response = $this->actingAs($this->tenant)->get('/owner/dashboard');
        $response->assertStatus(403);
    }

    public function test_tenant_can_access_tenant_dashboard(): void
    {
        $response = $this->actingAs($this->tenant)->get('/penghuni/dashboard');
        $response->assertStatus(200);
    }

    public function test_owner_cannot_access_tenant_routes(): void
    {
        $response = $this->actingAs($this->owner)->get('/penghuni/dashboard');
        $response->assertStatus(403);
    }

    public function test_owner_can_view_rooms_and_create_room(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/kamar');
        $response->assertStatus(200);

        $roomNumber = 'K-' . rand(1000, 9999);
        $createResponse = $this->actingAs($this->owner)->post('/owner/kamar', [
            'nomor_kamar' => $roomNumber,
            'lantai'      => '3',
            'kapasitas'   => 2,
            'deskripsi'   => 'Kamar pengujian otomatis',
            'status'      => 'tersedia',
        ]);

        $createResponse->assertRedirect(route('owner.rooms.index'));
        $this->assertDatabaseHas('kamar', ['nomor_kamar' => $roomNumber]);
    }

    public function test_tenant_occupant_count_validation_against_capacity(): void
    {
        $testRoom = Room::create([
            'nomor_kamar' => 'CAP-1',
            'lantai'      => '1',
            'kapasitas'   => 1,
            'status'      => 'tersedia',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/penghuni/tambah', [
            'nama'            => 'Penghuni Kelebihan',
            'email'           => 'over_capacity_' . rand(100, 999) . '@test.com',
            'kata_sandi'      => 'katasandi123',
            'kamar_id'        => $testRoom->id,
            'tanggal_masuk'   => now()->toDateString(),
            'jumlah_penghuni' => 3, // Lebih besar dari kapasitas 1
        ]);

        $response->assertSessionHasErrors(['jumlah_penghuni']);
    }

    public function test_tenant_can_create_report(): void
    {
        // Pastikan tenant memiliki stay aktif
        $stay = TenantStay::firstOrCreate([
            'pengguna_id' => $this->tenant->id,
            'kamar_id'    => $this->room->id,
            'status'      => 'aktif',
        ], [
            'tanggal_masuk'   => now()->subMonths(1),
            'jumlah_penghuni' => 1,
        ]);

        $response = $this->actingAs($this->tenant)->post('/penghuni/laporan/buat', [
            'judul'            => 'Lampu Kamar Mandi Mati Otomatis Test',
            'kategori_id'      => $this->category->id,
            'deskripsi'        => 'Deskripsi laporan minimal sepuluh karakter untuk pengujian.',
            'lokasi'           => 'Kamar Mandi',
            'tanggal_kejadian' => now()->toDateString(),
            'prioritas'        => 'sedang',
        ]);

        $response->assertRedirect(route('tenant.reports.index'));
        $this->assertDatabaseHas('laporan', [
            'judul'       => 'Lampu Kamar Mandi Mati Otomatis Test',
            'pengguna_id' => $this->tenant->id,
        ]);
    }

    public function test_tenant_cannot_view_other_tenants_private_report(): void
    {
        $otherTenant = User::create([
            'nama'                 => 'Penghuni Lain',
            'email'                => 'other_' . rand(100, 999) . '@test.com',
            'password'             => bcrypt('katasandi123'),
            'peran'                => 'penghuni',
            'is_aktif'             => true,
            'wajib_ganti_password' => false,
        ]);

        $report = Report::create([
            'pengguna_id'      => $otherTenant->id,
            'kategori_id'      => $this->category->id,
            'judul'            => 'Laporan Rahasia Lain',
            'deskripsi'        => 'Deskripsi laporan rahasia milik penghuni lain.',
            'tanggal_kejadian' => now()->toDateString(),
            'prioritas'        => 'sedang',
            'status'           => 'menunggu',
        ]);

        $response = $this->actingAs($this->tenant)->get("/penghuni/laporan/{$report->id}");
        $response->assertStatus(403);
    }

    public function test_owner_can_respond_and_update_report_status(): void
    {
        $report = Report::create([
            'pengguna_id'      => $this->tenant->id,
            'kategori_id'      => $this->category->id,
            'judul'            => 'Laporan Status Test',
            'deskripsi'        => 'Deskripsi laporan untuk pengujian respon dan status.',
            'tanggal_kejadian' => now()->toDateString(),
            'prioritas'        => 'tinggi',
            'status'           => 'menunggu',
        ]);

        // Respon pemilik
        $response = $this->actingAs($this->owner)->post("/owner/laporan/{$report->id}/tanggapan", [
            'tanggapan' => 'Segera kami perbaiki hari ini.',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tanggapan_laporan', [
            'laporan_id' => $report->id,
            'tanggapan'  => 'Segera kami perbaiki hari ini.',
        ]);

        // Update status ke diproses
        $statusResponse = $this->actingAs($this->owner)->post("/owner/laporan/{$report->id}/status", [
            'status'  => 'diproses',
            'catatan' => 'Teknisi sudah diberangkatkan.',
        ]);
        $statusResponse->assertSessionHas('success');
        $this->assertDatabaseHas('laporan', [
            'id'     => $report->id,
            'status' => 'diproses',
        ]);
    }

    public function test_logout_redirects_and_clears_session(): void
    {
        $response = $this->actingAs($this->owner)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_owner_can_view_tenant_stay_history_with_active_status_and_formatted_duration(): void
    {
        $stay = TenantStay::firstOrCreate([
            'pengguna_id' => $this->tenant->id,
            'kamar_id'    => $this->room->id,
            'status'      => 'aktif',
        ], [
            'tanggal_masuk'   => now()->subDays(5)->toDateString(),
            'jumlah_penghuni' => 1,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/penghuni/riwayat');
        $response->assertStatus(200);
        $response->assertSee('Aktif');
        $response->assertSee('5 hari');
        $this->assertTrue($stay->is_active);
    }
}
