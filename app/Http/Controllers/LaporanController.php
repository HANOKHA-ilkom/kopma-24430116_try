<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use App\Models\TransaksiSimpanan;
use App\Models\Pinjaman;
use App\Models\AngsuranPinjaman;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Member;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // 1. Rekap Simpanan
        $totalSimpananPokok = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'pokok'))->sum('saldo');
        $totalSimpananWajib = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'wajib'))->sum('saldo');
        $totalSimpananSukarela = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))->sum('saldo');
        $totalSeluruhSimpanan = $totalSimpananPokok + $totalSimpananWajib + $totalSimpananSukarela;

        // 2. Rekap Pinjaman
        $pinjamanDisalurkan = Pinjaman::where('status', 'disetujui')->sum('jumlah_pinjaman');
        $sisaPinjaman = Pinjaman::where('status', 'disetujui')->sum('sisa_pinjaman');
        $angsuranTerbayar = AngsuranPinjaman::where('status', 'lunas')->sum('nominal_angsuran');
        $bungaTerkumpul = AngsuranPinjaman::where('status', 'lunas')->sum('bunga');

        // 3. Rekap Toko / Penjualan
        $penjualanQuery = Penjualan::where('status_pembayaran', 'lunas')
            ->whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun);

        $omsetToko = $penjualanQuery->sum('total_akhir');
        $totalTransaksiToko = $penjualanQuery->count();

        // Estimasi Laba Kotor Toko
        $details = DetailPenjualan::whereHas('penjualan', function ($q) use ($bulan, $tahun) {
            $q->where('status_pembayaran', 'lunas')
              ->whereMonth('tanggal_transaksi', $bulan)
              ->whereYear('tanggal_transaksi', $tahun);
        })->with('produk')->get();

        $modalBarangTerjual = $details->sum(fn($d) => $d->produk->harga_beli * $d->jumlah);
        $labaKotorToko = $omsetToko - $modalBarangTerjual;

        return view('laporan.index', compact(
            'bulan',
            'tahun',
            'totalSimpananPokok',
            'totalSimpananWajib',
            'totalSimpananSukarela',
            'totalSeluruhSimpanan',
            'pinjamanDisalurkan',
            'sisaPinjaman',
            'angsuranTerbayar',
            'bungaTerkumpul',
            'omsetToko',
            'totalTransaksiToko',
            'modalBarangTerjual',
            'labaKotorToko'
        ));
    }
}
