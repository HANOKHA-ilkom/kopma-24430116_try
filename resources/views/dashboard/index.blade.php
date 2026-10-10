@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Koperasi')
@section('page_subtitle', 'Selamat datang, ' . Auth::user()->name . ' 👋')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Anggota -->
        <div class="glass-card rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Anggota Aktif</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalMembers) }}</h3>
                    <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Mahasiswa Terdaftar
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Simpanan -->
        <div class="glass-card rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Simpanan</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Pokok, Wajib, Sukarela
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Pinjaman Aktif -->
        <div class="glass-card rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Pinjaman Beredar</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalPinjamanAktif, 0, ',', '.') }}</h3>
                    <p class="text-xs text-amber-600 font-semibold mt-1">
                        Sisa tagihan pinjaman
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i data-lucide="banknote" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Omset Kasir Toko -->
        <div class="glass-card rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Omset Toko (Bulan Ini)</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</h3>
                    <p class="text-xs text-blue-600 font-semibold mt-1">
                        Unit Usaha Minimarket
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Bar -->
    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Aksi Cepat</h4>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Buka Kasir POS Toko</span>
            </a>
            <a href="{{ route('simpanans.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs shadow-sm transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-600"></i>
                <span>Setor / Tarik Simpanan</span>
            </a>
            <a href="{{ route('pinjamans.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs shadow-sm transition-all">
                <i data-lucide="hand-coins" class="w-4 h-4 text-amber-600"></i>
                <span>Pengajuan Pinjaman</span>
            </a>
            @if(Auth::user()->role !== 'anggota')
            <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs shadow-sm transition-all">
                <i data-lucide="user-plus" class="w-4 h-4 text-blue-600"></i>
                <span>Daftarkan Anggota Baru</span>
            </a>
            <a href="{{ route('produks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs shadow-sm transition-all">
                <i data-lucide="package" class="w-4 h-4 text-purple-600"></i>
                <span>Kelola Stok Produk</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Tampilan Khusus Anggota -->
    @if(Auth::user()->role === 'anggota' && $memberData)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kartu Anggota Digital -->
        <div class="lg:col-span-1 rounded-2xl bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 text-white p-6 shadow-xl relative overflow-hidden border border-emerald-800/40">
            <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-emerald-500/10 blur-xl"></div>
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">KARTU ANGGOTA KOPMA</span>
                    <h4 class="text-lg font-black mt-0.5">{{ $memberData['member']->user->name }}</h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center font-black text-emerald-400">
                    K
                </div>
            </div>

            <div class="space-y-2 text-xs text-slate-300 mb-6">
                <div class="flex justify-between">
                    <span class="text-slate-400">No. Anggota:</span>
                    <span class="font-mono font-bold text-white">{{ $memberData['member']->nomor_anggota }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">NIM:</span>
                    <span class="font-mono font-bold text-white">{{ $memberData['member']->nim }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Program Studi:</span>
                    <span class="font-semibold text-white">{{ $memberData['member']->program_studi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Fakultas:</span>
                    <span class="text-white">{{ $memberData['member']->fakultas }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-emerald-900/60 flex justify-between items-center">
                <span class="text-xs text-emerald-400 font-medium">Status Keanggotaan</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold uppercase border border-emerald-500/30">
                    {{ $memberData['member']->status }}
                </span>
            </div>
        </div>

        <!-- Rincian Saldo Simpanan Anggota -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                <span>Saldo Simpanan Saya</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($memberData['simpanans'] as $s)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs font-semibold text-slate-500 uppercase">{{ $s->jenisSimpanan->nama }}</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($s->saldo, 0, ',', '.') }}</h5>
                    <p class="text-xs text-slate-400 font-mono mt-1">{{ $s->nomor_rekening }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Breakdown Simpanan Koperasi & Notifikasi Approval -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Breakdown Simpanan -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm lg:col-span-1">
            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-600"></i>
                <span>Komposisi Simpanan KOPMA</span>
            </h4>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-600">Simpanan Pokok</span>
                        <span class="text-slate-900">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $totalSimpanan > 0 ? ($simpananPokok / $totalSimpanan * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-600">Simpanan Wajib</span>
                        <span class="text-slate-900">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-teal-500 rounded-full" style="width: {{ $totalSimpanan > 0 ? ($simpananWajib / $totalSimpanan * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-600">Simpanan Sukarela</span>
                        <span class="text-slate-900">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalSimpanan > 0 ? ($simpananSukarela / $totalSimpanan * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs">
                <span class="text-slate-500 font-medium">Total Akumulasi</span>
                <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Pinjaman Menunggu Approval / Transaksi Terakhir -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                    <span>Pengajuan Pinjaman Menunggu Verifikasi</span>
                </h4>
                <a href="{{ route('pinjamans.index') }}" class="text-xs text-emerald-600 font-bold hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($pendingLoans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase font-semibold">
                            <th class="py-2.5">Kode</th>
                            <th class="py-2.5">Nama Anggota</th>
                            <th class="py-2.5">Jumlah</th>
                            <th class="py-2.5">Tenor</th>
                            <th class="py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($pendingLoans as $pl)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 font-mono font-bold text-slate-800">{{ $pl->kode_pinjaman }}</td>
                            <td class="py-3 text-slate-700">{{ $pl->member->user->name }}</td>
                            <td class="py-3 font-bold text-slate-900">Rp {{ number_format($pl->jumlah_pinjaman, 0, ',', '.') }}</td>
                            <td class="py-3 text-slate-600">{{ $pl->tenor_bulan }} Bulan</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('pinjamans.show', $pl->id) }}" class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 border border-emerald-200 transition-all">
                                    Tinjau
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                <p class="text-xs text-slate-600 font-medium">Tidak ada pengajuan pinjaman yang tertunda.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Transactions & Toko Sales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mutasi Simpanan Terbaru -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="arrow-left-right" class="w-4 h-4 text-emerald-600"></i>
                    <span>Mutasi Simpanan Terakhir</span>
                </h4>
                <a href="{{ route('simpanans.index') }}" class="text-xs text-emerald-600 font-bold hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentTransactions as $rt)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $rt->jenis_transaksi === 'setoran' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-bold">
                            <i data-lucide="{{ $rt->jenis_transaksi === 'setoran' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $rt->simpanan->member->user->name }}</p>
                            <span class="text-[11px] text-slate-500">{{ $rt->simpanan->jenisSimpanan->nama }} &bull; {{ date('d/m/Y H:i', strtotime($rt->tanggal_transaksi)) }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-extrabold {{ $rt->jenis_transaksi === 'setoran' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $rt->jenis_transaksi === 'setoran' ? '+' : '-' }} Rp {{ number_format($rt->jumlah, 0, ',', '.') }}
                        </p>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $rt->kode_transaksi }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada transaksi simpanan.</p>
                @endforelse
            </div>
        </div>

        <!-- Penjualan Kasir POS Terakhir -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-blue-600"></i>
                    <span>Penjualan Toko POS Terakhir</span>
                </h4>
                <a href="{{ route('pos.index') }}" class="text-xs text-emerald-600 font-bold hover:underline">
                    Buka Kasir &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentSales as $rs)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">
                                {{ $rs->member ? $rs->member->user->name : 'Pembeli Umum' }}
                            </p>
                            <span class="text-[11px] text-slate-500">
                                Metode: <span class="uppercase font-semibold">{{ $rs->metode_pembayaran }}</span> &bull; {{ date('d/m/Y H:i', strtotime($rs->tanggal_transaksi)) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-extrabold text-slate-900">
                            Rp {{ number_format($rs->total_akhir, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('pos.struk', $rs->id) }}" class="text-[11px] text-emerald-600 hover:underline font-semibold">
                            Cetak Struk
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada transaksi penjualan toko.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
