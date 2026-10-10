<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\AngsuranPinjaman;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pinjaman::with(['member.user', 'approver']);

        if ($user->role === 'anggota' && $user->member) {
            $query->where('member_id', $user->member->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pinjamans = $query->latest()->paginate(10)->withQueryString();

        $totalDipinjamkan = Pinjaman::where('status', 'disetujui')->sum('jumlah_pinjaman');
        $totalSisaPinjaman = Pinjaman::where('status', 'disetujui')->sum('sisa_pinjaman');
        $totalMenunggu = Pinjaman::where('status', 'pengajuan')->count();

        return view('pinjamans.index', compact(
            'pinjamans',
            'totalDipinjamkan',
            'totalSisaPinjaman',
            'totalMenunggu'
        ));
    }

    public function create()
    {
        $members = Member::with('user')->where('status', 'aktif')->get();
        return view('pinjamans.create', compact('members'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $memberId = $user->role === 'anggota' && $user->member ? $user->member->id : $request->member_id;

        $request->validate([
            'member_id' => $user->role === 'anggota' ? 'nullable' : 'required|exists:members,id',
            'jumlah_pinjaman' => 'required|numeric|min:100000|max:10000000',
            'tenor_bulan' => 'required|integer|in:3,6,12,24',
        ]);

        // Cek apakah ada pinjaman aktif yang belum lunas
        $hasActiveLoan = Pinjaman::where('member_id', $memberId)
            ->whereIn('status', ['pengajuan', 'disetujui'])
            ->exists();

        if ($hasActiveLoan) {
            return back()->with('error', 'Anggota masih memiliki pinjaman aktif atau pengajuan yang sedang diproses.');
        }

        $bungaPersen = 1.0; // 1% per bulan
        $jumlahPinjam = $request->jumlah_pinjaman;
        $tenor = $request->tenor_bulan;
        $angsuranPerBulan = ($jumlahPinjam / $tenor) + ($jumlahPinjam * ($bungaPersen / 100));

        $pinjaman = Pinjaman::create([
            'member_id' => $memberId,
            'kode_pinjaman' => 'PJN-' . date('Ym') . '-' . strtoupper(Str::random(4)),
            'jumlah_pinjaman' => $jumlahPinjam,
            'bunga_persen' => $bungaPersen,
            'tenor_bulan' => $tenor,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'sisa_pinjaman' => $angsuranPerBulan * $tenor,
            'status' => 'pengajuan',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        return redirect()->route('pinjamans.show', $pinjaman->id)
            ->with('success', 'Pengajuan pinjaman berhasil diajukan dan sedang menunggu persetujuan pengurus.');
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with(['member.user', 'approver', 'angsurans'])->findOrFail($id);
        return view('pinjamans.show', compact('pinjaman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_ditolak' => 'nullable|string|max:255',
        ]);

        $pinjaman = Pinjaman::findOrFail($id);

        if ($pinjaman->status !== 'pengajuan') {
            return back()->with('error', 'Status pinjaman sudah tidak dapat diubah.');
        }

        DB::transaction(function () use ($pinjaman, $request) {
            if ($request->status === 'disetujui') {
                $pinjaman->update([
                    'status' => 'disetujui',
                    'tanggal_disetujui' => now()->toDateString(),
                    'approved_by' => Auth::id(),
                ]);

                // Buat jadwal angsuran 1 s/d tenor
                $pokok = $pinjaman->jumlah_pinjaman / $pinjaman->tenor_bulan;
                $bunga = $pinjaman->jumlah_pinjaman * ($pinjaman->bunga_persen / 100);

                for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
                    AngsuranPinjaman::create([
                        'pinjaman_id' => $pinjaman->id,
                        'angsuran_ke' => $i,
                        'nominal_angsuran' => $pinjaman->angsuran_per_bulan,
                        'pokok' => $pokok,
                        'bunga' => $bunga,
                        'denda' => 0,
                        'tanggal_jatuh_tempo' => now()->addMonths($i)->toDateString(),
                        'status' => 'belum_bayar',
                        'keterangan' => "Angsuran ke-$i",
                    ]);
                }
            } else {
                $pinjaman->update([
                    'status' => 'ditolak',
                    'alasan_ditolak' => $request->alasan_ditolak ?? 'Pengajuan tidak memenuhi syarat.',
                    'approved_by' => Auth::id(),
                ]);
            }
        });

        return back()->with('success', 'Status pengajuan pinjaman berhasil diperbarui menjadi ' . ucfirst($request->status));
    }

    public function bayarAngsuran(Request $request, $angsuranId)
    {
        $angsuran = AngsuranPinjaman::with('pinjaman')->findOrFail($angsuranId);

        if ($angsuran->status === 'lunas') {
            return back()->with('info', 'Angsuran ini sudah lunas sebelumnya.');
        }

        DB::transaction(function () use ($angsuran) {
            $angsuran->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
            ]);

            $pinjaman = $angsuran->pinjaman;
            $pinjaman->decrement('sisa_pinjaman', $angsuran->nominal_angsuran);

            // Cek jika seluruh angsuran sudah lunas
            $unpaidCount = AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)
                ->where('status', 'belum_bayar')
                ->count();

            if ($unpaidCount === 0) {
                $pinjaman->update([
                    'status' => 'lunas',
                    'sisa_pinjaman' => 0,
                ]);
            }
        });

        return back()->with('success', "Pembayaran angsuran ke-{$angsuran->angsuran_ke} berhasil dicatat!");
    }
}
