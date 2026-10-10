@extends('layouts.app')

@section('title', 'Detail Anggota - ' . $member->user->name)
@section('page_title', 'Profil Anggota KOPMA')
@section('page_subtitle', 'Informasi keanggotaan dan riwayat keuangan')

@section('content')
<div class="space-y-6">

    <!-- Profile Overview Card -->
    <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-emerald-700/20 flex-shrink-0">
                {{ strtoupper(substr($member->user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-xl font-black text-slate-900">{{ $member->user->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ $member->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ $member->status }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 mt-1.5 font-medium">
                    <span><strong class="text-slate-700">No. Anggota:</strong> <span class="font-mono text-emerald-700 font-bold">{{ $member->nomor_anggota }}</span></span>
                    <span>&bull;</span>
                    <span><strong class="text-slate-700">NIM:</strong> <span class="font-mono">{{ $member->nim }}</span></span>
                    <span>&bull;</span>
                    <span>{{ $member->program_studi }} (Angkatan {{ $member->angkatan }})</span>
                    <span>&bull;</span>
                    <span>{{ $member->fakultas }}</span>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400 mt-2">
                    <span><i data-lucide="mail" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ $member->user->email }}</span>
                    <span><i data-lucide="phone" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ $member->user->phone ?? '-' }}</span>
                    <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>Bergabung: {{ date('d M Y', strtotime($member->tanggal_bergabung)) }}</span>
                </div>
            </div>
        </div>

        @if(Auth::user()->role !== 'anggota')
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('members.edit', $member->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit Profil</span>
            </a>
            <a href="{{ route('members.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-all flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Savings Accounts Grid -->
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
            <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
            <span>Rekening Simpanan Anggota</span>
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach($member->simpanans as $s)
            <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $s->jenisSimpanan->nama }}</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold">{{ $s->nomor_rekening }}</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($s->saldo, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">{{ $s->jenisSimpanan->keterangan }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Tabs / Sections: Pinjaman & Riwayat Mutasi Simpanan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pinjaman Aktif -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="banknote" class="w-4 h-4 text-amber-600"></i>
                    <span>Pinjaman Anggota</span>
                </span>
                @if(!$pinjamanAktif)
                <a href="{{ route('pinjamans.create') }}?member_id={{ $member->id }}" class="text-xs text-emerald-600 hover:underline font-bold">
                    + Ajukan Pinjaman
                </a>
                @endif
            </h4>

            @if($member->pinjamans->count() > 0)
            <div class="space-y-3">
                @foreach($member->pinjamans as $p)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-mono text-xs font-bold text-slate-900">{{ $p->kode_pinjaman }}</span>
                            <p class="text-[11px] text-slate-500">Diajukan: {{ date('d M Y', strtotime($p->tanggal_pengajuan)) }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $p->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'lunas' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $p->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-xs py-2 border-t border-slate-200/60 mt-2">
                        <div>
                            <span class="text-slate-400">Total:</span>
                            <p class="font-bold text-slate-800">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400">Tenor:</span>
                            <p class="font-bold text-slate-800">{{ $p->tenor_bulan }} Bulan</p>
                        </div>
                        <div>
                            <span class="text-slate-400">Sisa Tagihan:</span>
                            <p class="font-bold text-rose-700">Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-slate-200/60 text-right">
                        <a href="{{ route('pinjamans.show', $p->id) }}" class="text-xs text-emerald-700 font-bold hover:underline">
                            Lihat Jadwal Angsuran &rarr;
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-slate-400 text-xs">
                <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                Anggota ini tidak memiliki pinjaman aktif.
            </div>
            @endif
        </div>

        <!-- Riwayat Transaksi Toko KOPMA -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-600"></i>
                <span>Aktivitas Belanja di Toko KOPMA</span>
            </h4>

            @if($member->penjualans->count() > 0)
            <div class="space-y-2.5">
                @foreach($member->penjualans as $penjualan)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-mono font-bold text-slate-800">{{ $penjualan->kode_transaksi }}</p>
                        <span class="text-[11px] text-slate-400">{{ date('d M Y H:i', strtotime($penjualan->tanggal_transaksi)) }} &bull; {{ strtoupper($penjualan->metode_pembayaran) }}</span>
                    </div>
                    <div class="text-right">
                        <p class="font-extrabold text-slate-900">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</p>
                        <a href="{{ route('pos.struk', $penjualan->id) }}" class="text-[11px] text-emerald-600 hover:underline font-semibold">
                            Lihat Struk
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-slate-400 text-xs">
                <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                Belum ada transaksi belanja di kasir toko KOPMA.
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
