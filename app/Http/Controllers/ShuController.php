<?php

namespace App\Http\Controllers;

use App\Models\PembagianShu;
use App\Models\DetailShuAnggota;
use App\Models\Member;
use App\Models\Simpanan;
use App\Models\Penjualan;
use App\Models\TransaksiSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShuController extends Controller
{
    public function index(Request $request)
    {
        $shuList = PembagianShu::with('details.member.user')->latest()->get();

        $selectedShu = null;
        if ($request->filled('id')) {
            $selectedShu = PembagianShu::with('details.member.user')->find($request->id);
        } else {
            $selectedShu = $shuList->first();
        }

        $user = Auth::user();
        $myShu = null;
        if ($user->role === 'anggota' && $user->member && $selectedShu) {
            $myShu = $selectedShu->details->firstWhere('member_id', $user->member->id);
        }

        return view('shu.index', compact('shuList', 'selectedShu', 'myShu'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tahun_buku' => 'required|digits:4',
            'total_shu' => 'required|numeric|min:100000',
            'persen_jasa_modal' => 'required|numeric|min:1|max:99',
        ]);

        $tahun = $request->tahun_buku;
        $totalShu = $request->total_shu;
        $persenModal = $request->persen_jasa_modal;
        $persenUsaha = 100 - $persenModal;

        // Cek jika tahun buku sudah ada
        if (PembagianShu::where('tahun_buku', $tahun)->exists()) {
            return back()->with('error', "Pembagian SHU untuk Tahun Buku {$tahun} sudah pernah dihitung.");
        }

        DB::transaction(function () use ($tahun, $totalShu, $persenModal, $persenUsaha) {
            $pembagian = PembagianShu::create([
                'tahun_buku' => $tahun,
                'total_shu' => $totalShu,
                'persen_jasa_modal' => $persenModal,
                'persen_jasa_usaha' => $persenUsaha,
                'status' => 'final',
                'tanggal_pembagian' => now()->toDateString(),
            ]);

            $totalPoolModal = $totalShu * ($persenModal / 100);
            $totalPoolUsaha = $totalShu * ($persenUsaha / 100);

            $members = Member::where('status', 'aktif')->with('simpanans')->get();

            // Total seluruh simpanan anggota (Pokok + Wajib)
            $totalSimpananSeluruhAnggota = Simpanan::whereHas('member', fn($q) => $q->where('status', 'aktif'))
                ->whereHas('jenisSimpanan', fn($q) => $q->whereIn('tipe', ['pokok', 'wajib']))
                ->sum('saldo');

            // Total seluruh transaksi belanja anggota
            $totalBelanjaSeluruhAnggota = Penjualan::whereNotNull('member_id')
                ->where('status_pembayaran', 'lunas')
                ->sum('total_akhir');

            foreach ($members as $m) {
                // Simpanan anggota ini
                $simpananMember = $m->simpanans->whereIn('jenis_simpanan_id', [1, 2])->sum('saldo');
                $proporsiModal = $totalSimpananSeluruhAnggota > 0 ? ($simpananMember / $totalSimpananSeluruhAnggota) : 0;
                $shuModal = round($totalPoolModal * $proporsiModal);

                // Belanja anggota ini
                $belanjaMember = Penjualan::where('member_id', $m->id)
                    ->where('status_pembayaran', 'lunas')
                    ->sum('total_akhir');
                $proporsiUsaha = $totalBelanjaSeluruhAnggota > 0 ? ($belanjaMember / $totalBelanjaSeluruhAnggota) : (1 / max(1, $members->count()));
                $shuUsaha = round($totalPoolUsaha * $proporsiUsaha);

                DetailShuAnggota::create([
                    'pembagian_shu_id' => $pembagian->id,
                    'member_id' => $m->id,
                    'shu_jasa_modal' => $shuModal,
                    'shu_jasa_usaha' => $shuUsaha,
                    'total_shu_diterima' => $shuModal + $shuUsaha,
                    'status_pencairan' => 'belum_dicairkan',
                ]);
            }
        });

        return back()->with('success', "Pembagian SHU Tahun Buku {$tahun} berhasil dikalkulasi!");
    }

    public function cairkan(Request $request, $id)
    {
        $detail = DetailShuAnggota::with('member')->findOrFail($id);

        if ($detail->status_pencairan === 'sudah_dicairkan') {
            return back()->with('info', 'SHU ini sudah dicairkan sebelumnya.');
        }

        DB::transaction(function () use ($detail, $request) {
            $metode = $request->input('metode', 'saldo'); // saldo atau kas

            if ($metode === 'saldo') {
                // Masukkan ke simpanan sukarela
                $simpananSukarela = Simpanan::where('member_id', $detail->member_id)
                    ->whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))
                    ->first();

                if ($simpananSukarela) {
                    $simpananSukarela->increment('saldo', $detail->total_shu_diterima);

                    TransaksiSimpanan::create([
                        'simpanan_id' => $simpananSukarela->id,
                        'kode_transaksi' => 'TRX-SHU-' . strtoupper(Str::random(6)),
                        'jenis_transaksi' => 'setoran',
                        'jumlah' => $detail->total_shu_diterima,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Pencairan SHU ke Rekening Simpanan Sukarela',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $detail->update([
                'status_pencairan' => 'sudah_dicairkan',
                'tanggal_pencairan' => now(),
            ]);
        });

        return back()->with('success', "SHU Anggota {$detail->member->user->name} berhasil dicairkan!");
    }
}
