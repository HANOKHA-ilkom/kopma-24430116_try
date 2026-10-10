<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use App\Models\JenisSimpanan;
use App\Models\TransaksiSimpanan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $jenisList = JenisSimpanan::all();

        $query = Simpanan::with(['member.user', 'jenisSimpanan']);

        if ($request->filled('jenis_id')) {
            $query->where('jenis_simpanan_id', $request->jenis_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('nomor_anggota', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $simpanans = $query->paginate(15)->withQueryString();

        $totalPokok = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'pokok'))->sum('saldo');
        $totalWajib = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'wajib'))->sum('saldo');
        $totalSukarela = Simpanan::whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))->sum('saldo');
        $totalKeseluruhan = $totalPokok + $totalWajib + $totalSukarela;

        $recentTransactions = TransaksiSimpanan::with(['simpanan.member.user', 'simpanan.jenisSimpanan'])
            ->latest('tanggal_transaksi')
            ->take(10)
            ->get();

        $members = Member::with('user')->where('status', 'aktif')->get();

        return view('simpanans.index', compact(
            'simpanans',
            'jenisList',
            'totalPokok',
            'totalWajib',
            'totalSukarela',
            'totalKeseluruhan',
            'recentTransactions',
            'members'
        ));
    }

    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'simpanan_id' => 'required|exists:simpanans,id',
            'jenis_transaksi' => 'required|in:setoran,penarikan',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $simpanan = Simpanan::with('jenisSimpanan')->findOrFail($request->simpanan_id);

        if ($request->jenis_transaksi === 'penarikan') {
            if ($simpanan->jenisSimpanan->tipe !== 'sukarela') {
                return back()->with('error', 'Hanya Simpanan Sukarela yang dapat ditarik sewaktu-waktu.');
            }
            if ($simpanan->saldo < $request->jumlah) {
                return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini.');
            }
        }

        DB::transaction(function () use ($request, $simpanan) {
            $trx = TransaksiSimpanan::create([
                'simpanan_id' => $simpanan->id,
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)),
                'jenis_transaksi' => $request->jenis_transaksi,
                'jumlah' => $request->jumlah,
                'tanggal_transaksi' => now(),
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            if ($request->jenis_transaksi === 'setoran') {
                $simpanan->increment('saldo', $request->jumlah);
            } else {
                $simpanan->decrement('saldo', $request->jumlah);
            }
        });

        return back()->with('success', 'Transaksi ' . ucfirst($request->jenis_transaksi) . ' berhasil diproses.');
    }
}
