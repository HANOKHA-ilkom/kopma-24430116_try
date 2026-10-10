<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Simpanan;
use App\Models\TransaksiSimpanan;
use App\Models\Pinjaman;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\PembagianShu;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik Utama
        $totalMembers = Member::where('status', 'aktif')->count();
        $totalSimpanan = Simpanan::sum('saldo');
        $totalPinjamanAktif = Pinjaman::where('status', 'disetujui')->sum('sisa_pinjaman');
        $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_akhir');

        // Detail Simpanan per Tipe
        $simpananPokok = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'pokok'))->sum('saldo');
        $simpananWajib = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'wajib'))->sum('saldo');
        $simpananSukarela = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))->sum('saldo');

        // Transaksi Terbaru
        $recentTransactions = TransaksiSimpanan::with(['simpanan.member.user', 'simpanan.jenisSimpanan'])
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        $recentSales = Penjualan::with(['member.user', 'kasir'])
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        // Pinjaman Menunggu Approval
        $pendingLoans = Pinjaman::with('member.user')
            ->where('status', 'pengajuan')
            ->latest()
            ->take(5)
            ->get();

        // Stok Produk Menipis
        $lowStockProducts = Produk::whereColumn('stok', '<=', 'stok_minimal')
            ->take(5)
            ->get();

        // Jika login sebagai Anggota biasa, ambil data spesifik anggota
        $memberData = null;
        if ($user->role === 'anggota' && $user->member) {
            $memberData = [
                'member' => $user->member,
                'simpanans' => $user->member->simpanans()->with('jenisSimpanan')->get(),
                'pinjamans' => $user->member->pinjamans()->with('angsurans')->latest()->get(),
                'riwayat_transaksi' => TransaksiSimpanan::whereHas('simpanan', fn($q) => $q->where('member_id', $user->member->id))
                    ->latest('tanggal_transaksi')->take(5)->get(),
            ];
        }

        return view('dashboard.index', compact(
            'totalMembers',
            'totalSimpanan',
            'totalPinjamanAktif',
            'totalPenjualanBulanIni',
            'simpananPokok',
            'simpananWajib',
            'simpananSukarela',
            'recentTransactions',
            'recentSales',
            'pendingLoans',
            'lowStockProducts',
            'memberData'
        ));
    }
}
