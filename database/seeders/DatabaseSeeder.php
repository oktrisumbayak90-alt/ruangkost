<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\ReportResponse;
use App\Models\Room;
use App\Models\TenantStay;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Pemilik Kos ─────────────────────────────────────────
        $owner = User::withTrashed()->where('peran', 'pemilik')->first();
        if ($owner) {
            if ($owner->trashed()) {
                $owner->restore();
            }
        } else {
            $owner = User::create([
                'nama'                 => 'Oktri Sumbayak',
                'username'             => 'pemilik',
                'email'                => 'Adminkos@gmail.com',
                'password'             => Hash::make('Admin123'),
                'peran'                => 'pemilik',
                'telepon'              => '081234567890',
                'jenis_kelamin'        => 'laki-laki',
                'alamat'               => 'Jl. Djamin Ginting',
                'is_aktif'             => true,
                'wajib_ganti_password' => false,
            ]);
        }

        // ── 2. Kamar ──────────────────────────────────────────────
        $rooms = Room::all();
        if ($rooms->isEmpty()) {
            $roomData = [
                ['nomor_kamar' => '101', 'lantai' => '1', 'kapasitas' => 1, 'deskripsi' => 'Kamar standar lantai 1', 'status' => 'tersedia'],
                ['nomor_kamar' => '102', 'lantai' => '1', 'kapasitas' => 1, 'deskripsi' => 'Kamar standar lantai 1', 'status' => 'tersedia'],
                ['nomor_kamar' => '103', 'lantai' => '1', 'kapasitas' => 2, 'deskripsi' => 'Kamar besar, 2 tempat tidur', 'status' => 'tersedia'],
                ['nomor_kamar' => '201', 'lantai' => '2', 'kapasitas' => 1, 'deskripsi' => 'Kamar standar lantai 2', 'status' => 'tersedia'],
                ['nomor_kamar' => '202', 'lantai' => '2', 'kapasitas' => 1, 'deskripsi' => 'Kamar ber-AC lantai 2', 'status' => 'tersedia'],
                ['nomor_kamar' => '203', 'lantai' => '2', 'kapasitas' => 2, 'deskripsi' => 'Kamar eksekutif dengan balkon', 'status' => 'tersedia'],
            ];

            foreach ($roomData as $data) {
                $room = Room::withTrashed()->where('nomor_kamar', $data['nomor_kamar'])->first();
                if ($room) {
                    if ($room->trashed()) {
                        $room->restore();
                    }
                } else {
                    $room = Room::create($data);
                }
                $rooms->push($room);
            }
        }

        // ── 3. Kategori ───────────────────────────────────────────
        $categoryData = [
            ['nama' => 'Kebersihan',          'slug' => 'kebersihan',          'ikon' => 'bi-trash',        'warna' => '#10b981', 'urutan' => 1, 'deskripsi' => 'Laporan terkait kebersihan lingkungan kos'],
            ['nama' => 'Keamanan',            'slug' => 'keamanan',            'ikon' => 'bi-shield',       'warna' => '#ef4444', 'urutan' => 2, 'deskripsi' => 'Laporan terkait keamanan kos'],
            ['nama' => 'Kenyamanan',          'slug' => 'kenyamanan',          'ikon' => 'bi-emoji-smile',  'warna' => '#8b5cf6', 'urutan' => 3, 'deskripsi' => 'Laporan terkait kenyamanan tinggal'],
            ['nama' => 'Kerusakan Fasilitas', 'slug' => 'kerusakan-fasilitas', 'ikon' => 'bi-tools',        'warna' => '#f59e0b', 'urutan' => 4, 'deskripsi' => 'Laporan kerusakan fasilitas kos'],
            ['nama' => 'Kehilangan Barang',   'slug' => 'kehilangan-barang',   'ikon' => 'bi-bag-x',        'warna' => '#ec4899', 'urutan' => 5, 'deskripsi' => 'Laporan kehilangan atau penemuan barang'],
            ['nama' => 'Lainnya',             'slug' => 'lainnya',             'ikon' => 'bi-three-dots',   'warna' => '#6b7280', 'urutan' => 6, 'deskripsi' => 'Laporan lainnya yang tidak termasuk kategori di atas'],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $categories[] = Category::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_aktif' => true])
            );
        }

        // ── 4. Penghuni Demo (Hanya jika belum ada penghuni) ───────
        $tenants = User::where('peran', 'penghuni')->get();
        if ($tenants->isEmpty()) {
            $tenantData = [
                ['nama' => 'Budi Santoso', 'email' => 'budi@kos.local',  'username' => 'budi',  'room' => $rooms[0], 'jenis_kelamin' => 'laki-laki', 'telepon' => '081234567001', 'alamat' => 'Jl. Merdeka No. 10, Jakarta Pusat'],
                ['nama' => 'Siti Rahayu',  'email' => 'siti@kos.local',  'username' => 'siti',  'room' => $rooms[1], 'jenis_kelamin' => 'perempuan', 'telepon' => '081234567002', 'alamat' => 'Jl. Mawar No. 15, Bandung'],
            ];

            foreach ($tenantData as $data) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'nama'                 => $data['nama'],
                        'username'             => $data['username'],
                        'password'             => Hash::make('katasandi123'),
                        'peran'                => 'penghuni',
                        'telepon'              => $data['telepon'],
                        'jenis_kelamin'        => $data['jenis_kelamin'],
                        'alamat'               => $data['alamat'],
                        'is_aktif'             => true,
                        'wajib_ganti_password' => false,
                    ]
                );

                TenantStay::firstOrCreate(
                    ['pengguna_id' => $user->id, 'kamar_id' => $data['room']->id, 'status' => 'aktif'],
                    [
                        'tanggal_masuk'   => now()->subMonths(rand(1, 12)),
                        'jumlah_penghuni' => 1,
                    ]
                );

                $data['room']->updateStatus();
                $tenants->push($user);
            }
        }

        // ── 5. Laporan Demo ───────────────────────────────────────
        $reportSamples = [
            [
                'judul'       => 'Kamar mandi kotor dan saluran air bau',
                'category'    => 0,
                'prioritas'   => 'sedang',
                'status'      => 'menunggu',
                'deskripsi'   => 'Saluran pembuangan air di kamar mandi agak tersumbat dan menimbulkan bau tidak sedap. Mohon bantuan petugas kebersihan untuk segera dibersihkan.',
                'response'    => null,
            ],
            [
                'judul'       => 'Kunci gerendel pintu kamar macet',
                'category'    => 1,
                'prioritas'   => 'tinggi',
                'status'      => 'diproses',
                'deskripsi'   => 'Kunci gerendel pintu kamar agak macet dan sulit diputar dari luar. Khawatir rusak dan tidak bisa dibuka.',
                'response'    => 'Baik, laporan sudah diterima. Tukang kunci sedang dihubungi dan akan datang siang ini untuk mengganti silinder kunci.',
            ],
            [
                'judul'       => 'AC di kamar 201 tidak dingin',
                'category'    => 3,
                'prioritas'   => 'tinggi',
                'status'      => 'menunggu',
                'deskripsi'   => 'AC di kamar 201 sudah dinyalakan beberapa jam dengan suhu 18°C namun hanya mengeluarkan angin biasa dan tidak dingin sama sekali.',
                'response'    => null,
            ],
            [
                'judul'       => 'Kebisingan musik di malam hari',
                'category'    => 2,
                'prioritas'   => 'rendah',
                'status'      => 'selesai',
                'deskripsi'   => 'Ada suara musik dan obrolan yang cukup keras dari area teras luar hingga larut malam lewat pukul 23.30 WIB, mohon diingatkan penghuni lainnya.',
                'response'    => 'Sudah kami ingatkan kepada seluruh penghuni melalui pesan grup untuk mematuhi jam tenang kos setelah pukul 22.00 WIB.',
            ],
            [
                'judul'       => 'Lampu lorong lantai 2 padam',
                'category'    => 3,
                'prioritas'   => 'sedang',
                'status'      => 'diproses',
                'deskripsi'   => 'Lampu penerangan di lorong lantai 2 bagian belakang mati, sehingga kondisi lorong cukup gelap saat malam hari.',
                'response'    => 'Bohlam pengganti sudah dibeli, sore ini akan segera dipasangkan oleh penjaga kos.',
            ],
            [
                'judul'       => 'Dompet tertinggal di area parkir',
                'category'    => 4,
                'prioritas'   => 'tinggi',
                'status'      => 'menunggu',
                'deskripsi'   => 'Dompet kulit warna cokelat gelap tertinggal di dekat gerbang parkir tadi sore. Jika ada yang menemukan mohon segera infokan ke pengelola kos.',
                'response'    => null,
            ],
        ];

        if ($tenants->isNotEmpty() && Report::count() === 0) {
            foreach ($reportSamples as $idx => $sample) {
                $tenant = $tenants[$idx % count($tenants)];
                $stay = TenantStay::where('pengguna_id', $tenant->id)->where('status', 'aktif')->first();

                if (!$stay) continue;

                $report = Report::create([
                    'pengguna_id'        => $tenant->id,
                    'kategori_id'        => $categories[$sample['category']]->id,
                    'kamar_id'           => $stay->kamar_id,
                    'riwayat_tinggal_id' => $stay->id,
                    'judul'              => $sample['judul'],
                    'deskripsi'          => $sample['deskripsi'],
                    'lokasi'             => "Kamar {$stay->room->nomor_kamar}",
                    'tanggal_kejadian'   => now()->subDays(rand(1, 30)),
                    'prioritas'          => $sample['prioritas'],
                    'status'             => $sample['status'],
                ]);

                // Riwayat status laporan
                ReportHistory::create([
                    'laporan_id'  => $report->id,
                    'pengguna_id' => $tenant->id,
                    'status_lama' => null,
                    'status_baru' => 'menunggu',
                    'catatan'     => 'Laporan berhasil dibuat oleh penghuni.',
                ]);

                if ($sample['status'] === 'diproses' || $sample['status'] === 'selesai') {
                    ReportHistory::create([
                        'laporan_id'  => $report->id,
                        'pengguna_id' => $owner->id,
                        'status_lama' => 'menunggu',
                        'status_baru' => 'diproses',
                        'catatan'     => 'Laporan sedang diproses dan ditindaklanjuti oleh pemilik kos.',
                    ]);
                }

                if ($sample['status'] === 'selesai') {
                    ReportHistory::create([
                        'laporan_id'  => $report->id,
                        'pengguna_id' => $owner->id,
                        'status_lama' => 'diproses',
                        'status_baru' => 'selesai',
                        'catatan'     => 'Masalah telah selesai ditangani dengan baik.',
                    ]);
                }

                // Tanggapan resmi pemilik
                if ($sample['response']) {
                    ReportResponse::create([
                        'laporan_id'  => $report->id,
                        'pengguna_id' => $owner->id,
                        'tanggapan'   => $sample['response'],
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('');
        $this->command->info('──────────────────────────────────────────────');
        $this->command->info('👤 Login Pemilik Kos:');
        $this->command->info('   Nama       : ' . $owner->nama);
        $this->command->info('   Email      : ' . $owner->email);
        $this->command->info('   Username   : ' . $owner->username);
        $this->command->info('   Kata Sandi : katasandi123');
        $this->command->info('');
        $this->command->info('👤 Login Penghuni Demo:');
        $this->command->info('   Nama       : ' . $tenants[0]->nama);
        $this->command->info('   Email      : ' . $tenants[0]->email);
        $this->command->info('   Username   : ' . $tenants[0]->username);
        $this->command->info('   Kata Sandi : katasandi123');
        $this->command->info('──────────────────────────────────────────────');
    }
}
