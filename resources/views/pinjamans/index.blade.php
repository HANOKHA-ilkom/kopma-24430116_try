@extends('layouts.app')

@section('title', 'Pinjaman Anggota')
@section('page_title', 'Layanan Simpan Pinjam KOPMA')
@section('page_subtitle', 'Kelola pengajuan pembiayaan dan angsuran mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pinjaman Disetujui</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalDipinjamkan, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Akumulasi pokok pembiayaan</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sisa Tagihan Beredar</span>
            <h3 class="text-2xl font-black text-rose-700 mt-1">Rp {{ number_format($totalSisaPinjaman, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Belum jatuh tempo / belum lunas</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-amber-300 bg-amber-50/50 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Menunggu Verifikasi</span>
            <h3 class="text-2xl font-black text-amber-950 mt-1">{{ number_format($totalMenunggu) }} Pengajuan</h3>
            <p class="text-[11px] text-amber-700 font-semibold mt-1">Butuh persetujuan pengurus</p>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('pinjamans.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" onchange="this.form.submit()" class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                <option value="">Semua Status Pinjaman</option>
                <option value="pengajuan" {{ request('status') == 'pengajuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Aktif / Disetujui</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>

        <a href="{{ route('pinjamans.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Ajukan Pinjaman Baru</span>
        </a>
    </div>

    <!-- Pinjaman Table -->
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">Kode Pinjaman</th>
                        <th class="py-3.5 px-4">Nama Anggota</th>
                        <th class="py-3.5 px-4">Jumlah Pokok</th>
                        <th class="py-3.5 px-4">Tenor</th>
                        <th class="py-3.5 px-4">Angsuran / Bln</th>
                        <th class="py-3.5 px-4">Sisa Tagihan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($pinjamans as $p)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $p->kode_pinjaman }}</td>
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900">{{ $p->member->user->name }}</p>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $p->member->nim }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">{{ $p->tenor_bulan }} Bulan</td>
                        <td class="py-3.5 px-4 text-slate-900 font-semibold">
                            Rp {{ number_format($p->angsuran_per_bulan, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-rose-700">
                            Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $p->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'lunas' ? 'bg-blue-100 text-blue-800' : ($p->status === 'pengajuan' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('pinjamans.show', $p->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 font-bold text-slate-700 text-xs transition-all inline-flex items-center gap-1">
                                <span>Detail</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <i data-lucide="banknote" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            Belum ada riwayat pinjaman.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pinjamans->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $pinjamans->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
