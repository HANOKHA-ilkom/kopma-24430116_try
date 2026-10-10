<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['user', 'simpanans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_anggota', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(10)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|unique:members,nim',
            'program_studi' => 'required|string|max:100',
            'fakultas' => 'required|string|max:100',
            'angkatan' => 'required|string|max:4',
            'phone' => 'nullable|string|max:20',
        ]);

        // Buat User Akun
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'anggota',
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // Buat Member
        $nomorAnggota = 'KOPMA-' . date('Y') . '-' . str_pad(Member::count() + 1, 3, '0', STR_PAD_LEFT);
        $member = Member::create([
            'user_id' => $user->id,
            'nomor_anggota' => $nomorAnggota,
            'nim' => $request->nim,
            'program_studi' => $request->program_studi,
            'fakultas' => $request->fakultas,
            'angkatan' => $request->angkatan,
            'status' => 'aktif',
            'tanggal_bergabung' => now()->toDateString(),
        ]);

        // Auto buat 3 rekening simpanan
        $jenisList = JenisSimpanan::all();
        foreach ($jenisList as $j) {
            $prefix = match($j->tipe) {
                'pokok' => 'SP-',
                'wajib' => 'SW-',
                default => 'SS-',
            };

            Simpanan::create([
                'member_id' => $member->id,
                'jenis_simpanan_id' => $j->id,
                'nomor_rekening' => $prefix . $member->nim,
                'saldo' => 0,
            ]);
        }

        return redirect()->route('members.show', $member->id)
            ->with('success', 'Anggota berhasil didaftarkan dengan Nomor: ' . $nomorAnggota . '. Password default: password');
    }

    public function show($id)
    {
        $member = Member::with([
            'user',
            'simpanans.jenisSimpanan',
            'simpanans.transaksis' => fn($q) => $q->latest()->take(10),
            'pinjamans.angsurans',
            'penjualans' => fn($q) => $q->latest()->take(10),
        ])->findOrFail($id);

        $totalSimpanan = $member->simpanans->sum('saldo');
        $pinjamanAktif = $member->pinjamans->where('status', 'disetujui')->first();

        return view('members.show', compact('member', 'totalSimpanan', 'pinjamanAktif'));
    }

    public function edit($id)
    {
        $member = Member::with('user')->findOrFail($id);
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $member = Member::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'program_studi' => 'required|string|max:100',
            'fakultas' => 'required|string|max:100',
            'angkatan' => 'required|string|max:4',
            'status' => 'required|in:aktif,non_aktif,alumni',
            'phone' => 'nullable|string|max:20',
        ]);

        $member->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        $member->update([
            'program_studi' => $request->program_studi,
            'fakultas' => $request->fakultas,
            'angkatan' => $request->angkatan,
            'status' => $request->status,
        ]);

        return redirect()->route('members.show', $member->id)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }
}
