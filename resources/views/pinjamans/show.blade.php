@extends('layouts.app')

@section('title', 'Detail Pinjaman - ' . $pinjaman->kode_pinjaman)
@section('page_title', 'Detail Pinjaman & Jadwal Angsuran')
@section('page_subtitle', 'Pantau status pembiayaan dan pelunasan angsuran')

@section('content')
<div class="space-y-6">

    <!-- Overview Card -->
    <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono text-lg font-black text-slate-900">{{ $pinjaman->kode_pinjaman }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    {{ $pinjaman->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($pinjaman->status === 'lunas' ? 'bg-blue-100 text-blue-800' : ($pinjaman->status === 'pengajuan' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                    {{ $pinjaman->status }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-2 font-medium">
                <span>Peminjam: <strong class="text-slate-800 font-bold">{{ $pinjaman->member->user->name }}</strong> ({{ $pinjaman->member->nim }})</span>
                <span>&bull;</span>
                <span>Diajukan: {{ date('d M Y', strtotime($pinjaman->tanggal_pengajuan)) }}</span>
                @if($pinjaman->tanggal_disetujui)
                <span>&bull;</span>
                <span>Disetujui: {{ date('d M Y', strtotime($pinjaman->tanggal_disetujui)) }} oleh {{ $pinjaman->approver->name ?? 'Pengurus' }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('pinjamans.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Loan Metric Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pokok Pinjaman</span>
            <h4 class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</h4>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tenor Pinjaman</span>
            <h4 class="text-xl font-black text-slate-900 mt-1">{{ $pinjaman->tenor_bulan }} Bulan</h4>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Angsuran / Bulan</span>
            <h4 class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($pinjaman->angsuran_per_bulan, 0, ',', '.') }}</h4>
        </div>
        <div class="glass-card rounded-2xl p-4 border border-rose-200 bg-rose-50/40 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Sisa Tagihan</span>
            <h4 class="text-xl font-black text-rose-800 mt-1">Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</h4>
        </div>
    </div>

    <!-- Approval Banner (Jika status pengajuan dan user adalah Admin/Pengurus) -->
    @if($pinjaman->status === 'pengajuan' && Auth::user()->role !== 'anggota')
    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
            <div>
                <h5 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Verifikasi Pengurus Diperlukan</h5>
                <p class="text-xs text-amber-800 mt-0.5">Pengajuan ini belum disetujui. Apakah Anda ingin menyetujui dan menerbitkan jadwal angsuran?</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('pinjamans.status', $pinjaman->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="disetujui">
                <button type="submit" onclick="return confirm('Apakah Anda yakin menyetujui pinjaman ini? Jadwal angsuran akan otomatis dibuat.')"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all">
                    ✓ Setujui Pinjaman
                </button>
            </form>

            <form action="{{ route('pinjamans.status', $pinjaman->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="ditolak">
                <button type="submit" onclick="return confirm('Tolak pengajuan pinjaman ini?')"
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-700/20 transition-all">
                    ✕ Tolak Pengajuan
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Installment Schedule Table -->
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Jadwal Angsuran Pembayaran (Bulan 1 s/d {{ $pinjaman->tenor_bulan }})</h4>
                <p class="text-xs text-slate-500">Rincian pokok, bunga, tanggal jatuh tempo, dan status pembayaran</p>
            </div>
        </div>

        @if($pinjaman->angsurans->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">Angsuran Ke</th>
                        <th class="py-3.5 px-4">Jatuh Tempo</th>
                        <th class="py-3.5 px-4">Pokok</th>
                        <th class="py-3.5 px-4">Bunga (1%)</th>
                        <th class="py-3.5 px-4">Total Bayar</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tgl. Lunas</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($pinjaman->angsurans as $a)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-bold text-slate-900">Ke - {{ $a->angsuran_ke }}</td>
                        <td class="py-3.5 px-4 font-mono">{{ date('d M Y', strtotime($a->tanggal_jatuh_tempo)) }}</td>
                        <td class="py-3.5 px-4">Rp {{ number_format($a->pokok, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 text-slate-500">Rp {{ number_format($a->bunga, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">
                            Rp {{ number_format($a->nominal_angsuran, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $a->status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $a->status === 'lunas' ? '✓ Lunas' : 'Belum Bayar' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono">
                            {{ $a->tanggal_bayar ? date('d/m/Y H:i', strtotime($a->tanggal_bayar)) : '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            @if($a->status !== 'lunas')
                            <form action="{{ route('pinjamans.bayar', $a->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" onclick="return confirm('Konfirmasi pembayaran angsuran ke-{{ $a->angsuran_ke }} sejumlah Rp {{ number_format($a->nominal_angsuran, 0, ',', '.') }}?')"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all">
                                    Bayar Sekarang
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-emerald-600 font-bold">Terbayar</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-slate-400 text-xs">
            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
            Jadwal angsuran akan dibuat otomatis setelah pengajuan pinjaman disetujui oleh pengurus.
        </div>
        @endif
    </div>

</div>
@endsection
