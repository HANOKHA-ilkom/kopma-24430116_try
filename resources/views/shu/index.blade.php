@extends('layouts.app')

@section('title', 'Pembagian SHU')
@section('page_title', 'Sisa Hasil Usaha (SHU) KOPMA')
@section('page_subtitle', 'Kalkulasi dan distribusi keuntungan koperasi kepada anggota')

@section('content')
<div class="space-y-6">

    <!-- Konsep SHU Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white p-6 shadow-xl border border-emerald-700/40 relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">PRINSIP KOPERASI INDONESIA</span>
            <h3 class="text-xl font-black mt-1">Pembagian Sisa Hasil Usaha (SHU) Berkeadilan</h3>
            <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                SHU KOPMA dibagikan kepada anggota berdasarkan 2 pilar utama:
                <strong class="text-emerald-300">Jasa Modal</strong> (proporsi simpanan pokok dan wajib yang dimiliki anggota) dan
                <strong class="text-teal-300">Jasa Usaha</strong> (keaktifan anggota dalam bertransaksi/berbelanja di Toko KOPMA).
            </p>
        </div>
    </div>

    <!-- Khusus Anggota: Kartu SHU Saya -->
    @if(Auth::user()->role === 'anggota' && $myShu)
    <div class="glass-card rounded-2xl p-6 border border-emerald-300 bg-emerald-50/40 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Dividen Keuntungan Saya (Tahun Buku {{ $selectedShu->tahun_buku }})</span>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
            <div class="p-4 rounded-xl bg-white border border-emerald-200">
                <span class="text-xs text-slate-500">Jasa Modal (Simpanan)</span>
                <h4 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($myShu->shu_jasa_modal, 0, ',', '.') }}</h4>
            </div>
            <div class="p-4 rounded-xl bg-white border border-emerald-200">
                <span class="text-xs text-slate-500">Jasa Usaha (Belanja Toko)</span>
                <h4 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($myShu->shu_jasa_usaha, 0, ',', '.') }}</h4>
            </div>
            <div class="p-4 rounded-xl bg-emerald-600 text-white border border-emerald-700">
                <span class="text-xs text-emerald-200">Total SHU Diterima</span>
                <h4 class="text-xl font-black mt-1">Rp {{ number_format($myShu->total_shu_diterima, 0, ',', '.') }}</h4>
                <span class="text-[10px] uppercase font-bold tracking-wider mt-1 inline-block px-2 py-0.5 rounded bg-emerald-800">
                    Status: {{ str_replace('_', ' ', $myShu->status_pencairan) }}
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Hitung SHU Baru (Admin/Pengurus) -->
    @if(Auth::user()->role !== 'anggota')
    <div class="glass-card rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="calculator" class="w-4 h-4 text-emerald-600"></i>
            <span>Kalkulator & Pembagian SHU Baru</span>
        </h4>

        <form action="{{ route('shu.generate') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun Buku *</label>
                <input type="number" name="tahun_buku" value="{{ date('Y') }}" min="2020" max="2035" required
                    class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total SHU Dibagikan (Rp) *</label>
                <input type="number" name="total_shu" value="15000000" min="100000" step="100000" required
                    class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alokasi Jasa Modal (%)</label>
                <input type="number" name="persen_jasa_modal" value="40" min="10" max="90" required
                    class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:outline-none">
                <span class="text-[10px] text-slate-400 mt-1 block">Sisa otomatis untuk Jasa Usaha</span>
            </div>

            <div>
                <button type="submit" onclick="return confirm('Kalkulasi pembagian SHU sekarang?')"
                    class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    <span>Hitung & Bagikan SHU</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Daftar Rincian SHU per Anggota -->
    @if($selectedShu)
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold text-slate-900">
                    Rincian Pembagian SHU Tahun Buku {{ $selectedShu->tahun_buku }}
                </h4>
                <p class="text-xs text-slate-500">
                    Total Dana SHU: <strong class="text-slate-800">Rp {{ number_format($selectedShu->total_shu, 0, ',', '.') }}</strong> &bull;
                    Modal: {{ $selectedShu->persen_jasa_modal }}% &bull; Usaha: {{ $selectedShu->persen_jasa_usaha }}%
                </p>
            </div>

            <!-- Pilih Tahun Buku -->
            <form action="{{ route('shu.index') }}" method="GET" class="flex items-center gap-2">
                <select name="id" onchange="this.form.submit()" class="py-1.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                    @foreach($shuList as $sl)
                    <option value="{{ $sl->id }}" {{ $selectedShu->id == $sl->id ? 'selected' : '' }}>
                        Tahun Buku {{ $sl->tahun_buku }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">Nama Anggota</th>
                        <th class="py-3.5 px-4">NIM</th>
                        <th class="py-3.5 px-4">SHU Jasa Modal</th>
                        <th class="py-3.5 px-4">SHU Jasa Usaha</th>
                        <th class="py-3.5 px-4">Total SHU Diterima</th>
                        <th class="py-3.5 px-4">Status Pencairan</th>
                        @if(Auth::user()->role !== 'anggota')
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($selectedShu->details as $d)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $d->member->user->name }}</td>
                        <td class="py-3.5 px-4 font-mono text-slate-500">{{ $d->member->nim }}</td>
                        <td class="py-3.5 px-4">Rp {{ number_format($d->shu_jasa_modal, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4">Rp {{ number_format($d->shu_jasa_usaha, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 font-extrabold text-emerald-700 text-sm">
                            Rp {{ number_format($d->total_shu_diterima, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $d->status_pencairan === 'sudah_dicairkan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $d->status_pencairan === 'sudah_dicairkan' ? '✓ Sudah Dicairkan' : 'Belum Dicairkan' }}
                            </span>
                        </td>
                        @if(Auth::user()->role !== 'anggota')
                        <td class="py-3.5 px-4 text-right">
                            @if($d->status_pencairan !== 'sudah_dicairkan')
                            <form action="{{ route('shu.cairkan', $d->id) }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="metode" value="saldo">
                                <button type="submit" onclick="return confirm('Cairkan SHU sebesar Rp {{ number_format($d->total_shu_diterima, 0, ',', '.') }} langsung ke Rekening Simpanan Sukarela Anggota?')"
                                    class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all">
                                    Cairkan ke Simpanan
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-400 font-semibold">Tercairkan</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
