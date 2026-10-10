@extends('layouts.app')

@section('title', 'Laporan Keuangan KOPMA')
@section('page_title', 'Laporan Keuangan & Aktivitas Koperasi')
@section('page_subtitle', 'Rekapitulasi simpanan, pinjaman, dan omset usaha toko')

@section('content')
<div class="space-y-6">

    <!-- Filter & Print Action Bar -->
    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="bulan" class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endfor
            </select>

            <select name="tahun" class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-all">
                Tampilkan Periode
            </button>
        </form>

        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak / Ekspor PDF</span>
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white rounded-2xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8" id="reportPrintArea">
        <!-- Report Header -->
        <div class="text-center pb-6 border-b border-slate-200">
            <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase">LAPORAN KEUANGAN KOPERASI MAHASISWA</h2>
            <p class="text-xs text-slate-500 mt-1">Periode: {{ DateTime::createFromFormat('!m', (int)$bulan)->format('F') }} {{ $tahun }}</p>
            <p class="text-[11px] text-slate-400 font-mono mt-0.5">Database: kopma_24430116 &bull; Diterbitkan: {{ date('d F Y') }}</p>
        </div>

        <!-- Section 1: Posisi Simpanan Anggota -->
        <div>
            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span>1. Rekapitulasi Simpanan Anggota</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Simpanan Pokok</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($totalSimpananPokok, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Simpanan Wajib</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($totalSimpananWajib, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Simpanan Sukarela</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($totalSimpananSukarela, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <span class="text-xs text-emerald-800 font-bold">Total Modal Simpanan</span>
                    <h5 class="text-lg font-black text-emerald-950 mt-1">Rp {{ number_format($totalSeluruhSimpanan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Section 2: Pembiayaan & Pinjaman -->
        <div>
            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                <span>2. Kinerja Unit Simpan Pinjam</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Total Pinjaman Disalurkan</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($pinjamanDisalurkan, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Sisa Tagihan Beredar</span>
                    <h5 class="text-lg font-black text-rose-700 mt-1">Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Angsuran Pokok Kembali</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($angsuranTerbayar, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-xs text-amber-800 font-bold">Pendapatan Bunga Jasa Pinjaman</span>
                    <h5 class="text-lg font-black text-amber-950 mt-1">Rp {{ number_format($bungaTerkumpul, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Section 3: Unit Toko / Minimarket POS -->
        <div>
            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>3. Kinerja Unit Usaha Toko KOPMA</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Omset Penjualan (Kotor)</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($omsetToko, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Jumlah Transaksi Kasir</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">{{ number_format($totalTransaksiToko) }} Transaksi</h5>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500">Modal Pokok Barang (HPP)</span>
                    <h5 class="text-lg font-black text-slate-900 mt-1">Rp {{ number_format($modalBarangTerjual, 0, ',', '.') }}</h5>
                </div>
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <span class="text-xs text-blue-800 font-bold">Laba Kotor Toko</span>
                    <h5 class="text-lg font-black text-blue-950 mt-1">Rp {{ number_format($labaKotorToko, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Pengurus -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold text-slate-800 mt-1">Ketua KOPMA</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">Budi Santoso</p>
                <p class="text-[10px] text-slate-400">NIM: 22430001</p>
            </div>
            <div>
                <p>Dilaporkan Oleh,</p>
                <p class="font-bold text-slate-800 mt-1">Bendahara KOPMA</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">Siti Nurhaliza</p>
                <p class="text-[10px] text-slate-400">NIM: 24430102</p>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
    @media print {
        aside, header, footer, form, button {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        #reportPrintArea {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>
@endpush
@endsection
