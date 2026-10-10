@extends('layouts.app')

@section('title', 'Simpanan Anggota')
@section('page_title', 'Manajemen Simpanan KOPMA')
@section('page_subtitle', 'Kelola Simpanan Pokok, Wajib, dan Sukarela')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Simpanan Pokok</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalPokok, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Dibayar sekali di awal</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Simpanan Wajib</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalWajib, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Iuran bulanan anggota</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Simpanan Sukarela</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalSukarela, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Dapat ditarik / untuk belanja</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-emerald-300 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Total Seluruh Dana Simpanan</span>
            <h3 class="text-2xl font-black text-emerald-950 mt-1">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-emerald-700 font-semibold mt-1">Modal dasar KOPMA</p>
        </div>
    </div>

    <!-- Quick Action: Form Setor / Tarik Simpanan (Khusus Pengurus/Admin) -->
    @if(Auth::user()->role !== 'anggota')
    <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-600"></i>
            <span>Proses Transaksi Simpanan (Setoran / Penarikan)</span>
        </h4>

        <form action="{{ route('simpanans.transaksi') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Rekening Simpanan *</label>
                <select name="simpanan_id" required class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                    <option value="">-- Pilih Rekening Anggota --</option>
                    @foreach($simpanans as $s)
                    <option value="{{ $s->id }}">
                        {{ $s->member->user->name }} - {{ $s->jenisSimpanan->nama }} (Saldo: Rp {{ number_format($s->saldo, 0, ',', '.') }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Transaksi *</label>
                <select name="jenis_transaksi" required class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                    <option value="setoran">📥 Setoran (Masuk)</option>
                    <option value="penarikan">📤 Penarikan (Khusus Sukarela)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominal (Rp) *</label>
                <input type="number" name="jumlah" step="1000" min="1000" placeholder="10000" required
                    class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Eksekusi Transaksi</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Accounts Table -->
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Buku Tabungan & Rekening Simpanan Anggota</h4>
                <p class="text-xs text-slate-500">Daftar seluruh rekening simpanan terdaftar</p>
            </div>

            <form action="{{ route('simpanans.index') }}" method="GET" class="flex items-center gap-2">
                <select name="jenis_id" onchange="this.form.submit()" class="py-1.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisList as $j)
                    <option value="{{ $j->id }}" {{ request('jenis_id') == $j->id ? 'selected' : '' }}>
                        {{ $j->nama }}
                    </option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari anggota..."
                        class="pl-8 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-400"></i>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">No. Rekening</th>
                        <th class="py-3.5 px-4">Nama Anggota</th>
                        <th class="py-3.5 px-4">NIM</th>
                        <th class="py-3.5 px-4">Jenis Simpanan</th>
                        <th class="py-3.5 px-4">Saldo Saat Ini</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($simpanans as $s)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $s->nomor_rekening }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $s->member->user->name }}</td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">{{ $s->member->nim }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $s->jenisSimpanan->tipe === 'pokok' ? 'bg-emerald-100 text-emerald-800' : ($s->jenisSimpanan->tipe === 'wajib' ? 'bg-teal-100 text-teal-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $s->jenisSimpanan->nama }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">
                            Rp {{ number_format($s->saldo, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('members.show', $s->member_id) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                                Riwayat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400">Tidak ada data simpanan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($simpanans->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $simpanans->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
