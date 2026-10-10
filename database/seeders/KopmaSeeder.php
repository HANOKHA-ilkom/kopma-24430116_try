<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiSimpanan;
use App\Models\Pinjaman;
use App\Models\AngsuranPinjaman;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\PembagianShu;
use App\Models\DetailShuAnggota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KopmaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin, Pengurus, Anggota)
        $admin = User::create([
            'name' => 'Administrator Kopma',
            'email' => 'admin@kopma.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $pengurus = User::create([
            'name' => 'Budi Santoso (Ketua Kopma)',
            'email' => 'pengurus@kopma.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengurus',
            'phone' => '081298765432',
            'is_active' => true,
        ]);

        // 2. Seed Jenis Simpanan
        $spPokok = JenisSimpanan::create([
            'nama' => 'Simpanan Pokok',
            'tipe' => 'pokok',
            'nominal_minimal' => 50000,
            'keterangan' => 'Simpanan yang dibayarkan sekali saat pertama kali menjadi anggota.',
        ]);

        $spWajib = JenisSimpanan::create([
            'nama' => 'Simpanan Wajib',
            'tipe' => 'wajib',
            'nominal_minimal' => 10000,
            'keterangan' => 'Simpanan yang wajib dibayarkan setiap bulan oleh anggota.',
        ]);

        $spSukarela = JenisSimpanan::create([
            'nama' => 'Simpanan Sukarela',
            'tipe' => 'sukarela',
            'nominal_minimal' => 5000,
            'keterangan' => 'Simpanan bebas yang dapat disetor dan ditarik kapan saja.',
        ]);

        // 3. Seed Data Anggota & Simpanan
        $membersData = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@student.ac.id',
                'nim' => '24430101',
                'prodi' => 'Teknik Informatika',
                'fakultas' => 'Ilmu Komputer',
                'angkatan' => '2024',
                'nomor_anggota' => 'KOPMA-2024-001',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@student.ac.id',
                'nim' => '24430102',
                'prodi' => 'Manajemen',
                'fakultas' => 'Ekonomi & Bisnis',
                'angkatan' => '2024',
                'nomor_anggota' => 'KOPMA-2024-002',
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky.pratama@student.ac.id',
                'nim' => '24430103',
                'prodi' => 'Sistem Informasi',
                'fakultas' => 'Ilmu Komputer',
                'angkatan' => '2024',
                'nomor_anggota' => 'KOPMA-2024-003',
            ],
            [
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@student.ac.id',
                'nim' => '23430104',
                'prodi' => 'Akuntansi',
                'fakultas' => 'Ekonomi & Bisnis',
                'angkatan' => '2023',
                'nomor_anggota' => 'KOPMA-2023-012',
            ],
            [
                'name' => 'Dhani Ramadhan',
                'email' => 'dhani.ramadhan@student.ac.id',
                'nim' => '23430105',
                'prodi' => 'Teknik Elektro',
                'fakultas' => 'Teknik',
                'angkatan' => '2023',
                'nomor_anggota' => 'KOPMA-2023-018',
            ],
        ];

        $createdMembers = [];

        foreach ($membersData as $idx => $mData) {
            $u = User::create([
                'name' => $mData['name'],
                'email' => $mData['email'],
                'password' => Hash::make('password'),
                'role' => 'anggota',
                'phone' => '0857' . rand(10000000, 99999999),
                'is_active' => true,
            ]);

            $m = Member::create([
                'user_id' => $u->id,
                'nomor_anggota' => $mData['nomor_anggota'],
                'nim' => $mData['nim'],
                'program_studi' => $mData['prodi'],
                'fakultas' => $mData['fakultas'],
                'angkatan' => $mData['angkatan'],
                'status' => 'aktif',
                'tanggal_bergabung' => '2024-01-15',
            ]);

            $createdMembers[] = $m;

            // Simpanan Pokok
            $simpPokok = Simpanan::create([
                'member_id' => $m->id,
                'jenis_simpanan_id' => $spPokok->id,
                'nomor_rekening' => 'SP-' . $m->nim,
                'saldo' => 50000,
            ]);

            TransaksiSimpanan::create([
                'simpanan_id' => $simpPokok->id,
                'kode_transaksi' => 'TRX-SP-' . Str::random(6),
                'jenis_transaksi' => 'setoran',
                'jumlah' => 50000,
                'tanggal_transaksi' => '2024-01-15 09:00:00',
                'keterangan' => 'Setoran Awal Simpanan Pokok',
                'created_by' => $admin->id,
            ]);

            // Simpanan Wajib
            $simpWajib = Simpanan::create([
                'member_id' => $m->id,
                'jenis_simpanan_id' => $spWajib->id,
                'nomor_rekening' => 'SW-' . $m->nim,
                'saldo' => 120000,
            ]);

            TransaksiSimpanan::create([
                'simpanan_id' => $simpWajib->id,
                'kode_transaksi' => 'TRX-SW-' . Str::random(6),
                'jenis_transaksi' => 'setoran',
                'jumlah' => 120000,
                'tanggal_transaksi' => '2024-09-01 10:00:00',
                'keterangan' => 'Akumulasi Simpanan Wajib 12 Bulan',
                'created_by' => $admin->id,
            ]);

            // Simpanan Sukarela
            $saldoSukarela = rand(50000, 500000);
            $simpSukarela = Simpanan::create([
                'member_id' => $m->id,
                'jenis_simpanan_id' => $spSukarela->id,
                'nomor_rekening' => 'SS-' . $m->nim,
                'saldo' => $saldoSukarela,
            ]);

            TransaksiSimpanan::create([
                'simpanan_id' => $simpSukarela->id,
                'kode_transaksi' => 'TRX-SS-' . Str::random(6),
                'jenis_transaksi' => 'setoran',
                'jumlah' => $saldoSukarela,
                'tanggal_transaksi' => '2024-09-10 14:30:00',
                'keterangan' => 'Setoran Simpanan Sukarela',
                'created_by' => $pengurus->id,
            ]);
        }

        // 4. Seed Pinjaman & Angsuran untuk Member 1
        $memberPinjam = $createdMembers[0]; // Ahmad Fauzi
        $jumlahPinjam = 1500000;
        $tenor = 6;
        $bunga = 1.0; // 1%
        $angsuranPerBulan = ($jumlahPinjam / $tenor) + ($jumlahPinjam * ($bunga / 100));

        $pinjaman = Pinjaman::create([
            'member_id' => $memberPinjam->id,
            'kode_pinjaman' => 'PJN-2024-001',
            'jumlah_pinjaman' => $jumlahPinjam,
            'bunga_persen' => $bunga,
            'tenor_bulan' => $tenor,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'sisa_pinjaman' => $angsuranPerBulan * 4, // 2 angsuran sudah lunas
            'status' => 'disetujui',
            'tanggal_pengajuan' => '2024-06-01',
            'tanggal_disetujui' => '2024-06-02',
            'approved_by' => $pengurus->id,
        ]);

        for ($i = 1; $i <= $tenor; $i++) {
            $isPaid = $i <= 2;
            AngsuranPinjaman::create([
                'pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $i,
                'nominal_angsuran' => $angsuranPerBulan,
                'pokok' => $jumlahPinjam / $tenor,
                'bunga' => $jumlahPinjam * ($bunga / 100),
                'denda' => 0,
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime("2024-06-02 +$i month")),
                'tanggal_bayar' => $isPaid ? date('Y-m-d H:i:s', strtotime("2024-06-02 +$i month -2 day")) : null,
                'status' => $isPaid ? 'lunas' : 'belum_bayar',
                'keterangan' => $isPaid ? "Angsuran ke-$i dibayarkan tepat waktu" : "Angsuran ke-$i belum dibayar",
            ]);
        }

        // 5. Seed Kategori & Produk Toko KOPMA
        $katMakanan = KategoriProduk::create(['nama' => 'Makanan & Snack', 'slug' => 'makanan-snack', 'keterangan' => 'Camilan dan makanan siap makan']);
        $katMinuman = KategoriProduk::create(['nama' => 'Minuman', 'slug' => 'minuman', 'keterangan' => 'Minuman dingin dan hangat']);
        $katAtk = KategoriProduk::create(['nama' => 'Alat Tulis & Kantor', 'slug' => 'alat-tulis-kantor', 'keterangan' => 'Pena, buku, dan kelengkapan kuliah']);
        $katMerch = KategoriProduk::create(['nama' => 'Merchandise Kampus', 'slug' => 'merchandise-kampus', 'keterangan' => 'Kaos, lanyard, dan pernak-pernik']);

        $produksData = [
            // Makanan
            ['kat' => $katMakanan->id, 'kode' => 'PRD-001', 'nama' => 'Indomie Goreng Original', 'beli' => 2800, 'jual' => 3500, 'stok' => 120, 'satuan' => 'pcs'],
            ['kat' => $katMakanan->id, 'kode' => 'PRD-002', 'nama' => 'Pop Mie Rasa Ayam Bawang', 'beli' => 4500, 'jual' => 6000, 'stok' => 85, 'satuan' => 'cup'],
            ['kat' => $katMakanan->id, 'kode' => 'PRD-003', 'nama' => 'Chitato Sapi Panggang 68g', 'beli' => 9500, 'jual' => 12000, 'stok' => 45, 'satuan' => 'pcs'],
            // Minuman
            ['kat' => $katMinuman->id, 'kode' => 'PRD-004', 'nama' => 'Air Mineral Aqua 600ml', 'beli' => 2500, 'jual' => 3500, 'stok' => 200, 'satuan' => 'botol'],
            ['kat' => $katMinuman->id, 'kode' => 'PRD-005', 'nama' => 'Teh Botol Sosro 450ml', 'beli' => 4000, 'jual' => 5500, 'stok' => 90, 'satuan' => 'botol'],
            ['kat' => $katMinuman->id, 'kode' => 'PRD-006', 'nama' => 'Kopi Good Day Mochacinno', 'beli' => 1800, 'jual' => 2500, 'stok' => 150, 'satuan' => 'sachet'],
            // ATK
            ['kat' => $katAtk->id, 'kode' => 'PRD-007', 'nama' => 'Pulpen Pilot G2 Hitam 0.5mm', 'beli' => 12000, 'jual' => 15000, 'stok' => 60, 'satuan' => 'pcs'],
            ['kat' => $katAtk->id, 'kode' => 'PRD-008', 'nama' => 'Buku Tulis Campus 50 Lembar', 'beli' => 5000, 'jual' => 7000, 'stok' => 100, 'satuan' => 'pcs'],
            ['kat' => $katAtk->id, 'kode' => 'PRD-009', 'nama' => 'Map Plastik Button Folder A4', 'beli' => 3000, 'jual' => 4500, 'stok' => 75, 'satuan' => 'pcs'],
            // Merch
            ['kat' => $katMerch->id, 'kode' => 'PRD-010', 'nama' => 'Lanyard KOPMA Custom', 'beli' => 15000, 'jual' => 25000, 'stok' => 50, 'satuan' => 'pcs'],
            ['kat' => $katMerch->id, 'kode' => 'PRD-011', 'nama' => 'Kaos Polo Kopma (Size L)', 'beli' => 65000, 'jual' => 85000, 'stok' => 30, 'satuan' => 'pcs'],
        ];

        $createdProduks = [];
        foreach ($produksData as $pData) {
            $createdProduks[] = Produk::create([
                'kategori_produk_id' => $pData['kat'],
                'kode_produk' => $pData['kode'],
                'nama_produk' => $pData['nama'],
                'harga_beli' => $pData['beli'],
                'harga_jual' => $pData['jual'],
                'stok' => $pData['stok'],
                'stok_minimal' => 10,
                'satuan' => $pData['satuan'],
                'is_active' => true,
            ]);
        }

        // 6. Seed Transaksi Penjualan Toko (POS)
        $penjualan1 = Penjualan::create([
            'kode_transaksi' => 'POS-202410-001',
            'member_id' => $createdMembers[0]->id,
            'kasir_id' => $pengurus->id,
            'subtotal' => 25500,
            'diskon' => 0,
            'total_akhir' => 25500,
            'bayar' => 30000,
            'kembali' => 4500,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'tanggal_transaksi' => '2024-10-05 11:20:00',
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan1->id,
            'produk_id' => $createdProduks[0]->id, // Indomie
            'harga_satuan' => 3500,
            'jumlah' => 3,
            'subtotal' => 10500,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan1->id,
            'produk_id' => $createdProduks[6]->id, // Pulpen Pilot
            'harga_satuan' => 15000,
            'jumlah' => 1,
            'subtotal' => 15000,
        ]);

        $penjualan2 = Penjualan::create([
            'kode_transaksi' => 'POS-202410-002',
            'member_id' => $createdMembers[1]->id,
            'kasir_id' => $pengurus->id,
            'subtotal' => 37000,
            'diskon' => 2000, // Diskon Anggota
            'total_akhir' => 35000,
            'bayar' => 35000,
            'kembali' => 0,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'lunas',
            'tanggal_transaksi' => '2024-10-08 14:15:00',
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan2->id,
            'produk_id' => $createdProduks[9]->id, // Lanyard KOPMA
            'harga_satuan' => 25000,
            'jumlah' => 1,
            'subtotal' => 25000,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan2->id,
            'produk_id' => $createdProduks[2]->id, // Chitato
            'harga_satuan' => 12000,
            'jumlah' => 1,
            'subtotal' => 12000,
        ]);

        // 7. Seed Pembagian SHU (Tahun Buku 2024)
        $totalShu = 12500000;
        $shuDist = PembagianShu::create([
            'tahun_buku' => '2024',
            'total_shu' => $totalShu,
            'persen_jasa_modal' => 40.00,
            'persen_jasa_usaha' => 60.00,
            'status' => 'final',
            'tanggal_pembagian' => '2024-12-31',
        ]);

        foreach ($createdMembers as $m) {
            $jasaModal = rand(150000, 350000);
            $jasaUsaha = rand(200000, 500000);
            DetailShuAnggota::create([
                'pembagian_shu_id' => $shuDist->id,
                'member_id' => $m->id,
                'shu_jasa_modal' => $jasaModal,
                'shu_jasa_usaha' => $jasaUsaha,
                'total_shu_diterima' => $jasaModal + $jasaUsaha,
                'status_pencairan' => 'sudah_dicairkan',
                'tanggal_pencairan' => '2025-01-10 10:00:00',
            ]);
        }
    }
}
