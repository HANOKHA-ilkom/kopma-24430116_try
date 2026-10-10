@extends('layouts.app')

@section('title', 'Ajukan Pinjaman Baru')
@section('page_title', 'Form Pengajuan Pinjaman')
@section('page_subtitle', 'Layanan pembiayaan modal dan kebutuhan akademik mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Form Pengajuan -->
        <div class="md:col-span-2 glass-card rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Data Pengajuan Pembiayaan</h3>
                    <p class="text-xs text-slate-500">Bunga flat koperasi: 1.0% per bulan.</p>
                </div>
                <a href="{{ route('pinjamans.index') }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
                </a>
            </div>

            <form action="{{ route('pinjamans.store') }}" method="POST" id="loanForm" class="space-y-4">
                @csrf

                @if(Auth::user()->role !== 'anggota')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Anggota Peminjam *</label>
                    <select name="member_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Mahasiswa Terdaftar --</option>
                        @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ (old('member_id') == $m->id || request('member_id') == $m->id) ? 'selected' : '' }}>
                            {{ $m->user->name }} - {{ $m->nim }} ({{ $m->program_studi }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Pinjaman (Rp) *</label>
                    <input type="number" name="jumlah_pinjaman" id="inputJumlah" value="{{ old('jumlah_pinjaman', 1000000) }}" step="50000" min="100000" max="10000000" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="setNominal(500000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-600">500 Ribu</button>
                        <button type="button" onclick="setNominal(1000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-600">1 Juta</button>
                        <button type="button" onclick="setNominal(2000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-600">2 Juta</button>
                        <button type="button" onclick="setNominal(5000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold text-slate-600">5 Juta</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jangka Waktu (Tenor) *</label>
                    <div class="grid grid-cols-4 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="tenor_bulan" value="3" class="peer sr-only tenor-radio" onchange="calculateLoan()">
                            <div class="p-3 text-center rounded-xl border border-slate-200 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                3 Bulan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tenor_bulan" value="6" checked class="peer sr-only tenor-radio" onchange="calculateLoan()">
                            <div class="p-3 text-center rounded-xl border border-slate-200 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                6 Bulan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tenor_bulan" value="12" class="peer sr-only tenor-radio" onchange="calculateLoan()">
                            <div class="p-3 text-center rounded-xl border border-slate-200 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                12 Bulan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tenor_bulan" value="24" class="peer sr-only tenor-radio" onchange="calculateLoan()">
                            <div class="p-3 text-center rounded-xl border border-slate-200 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                24 Bulan
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pinjamans.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Simulation Calculator Sidebar -->
        <div class="md:col-span-1 rounded-2xl bg-gradient-to-br from-slate-900 to-emerald-950 text-white p-6 shadow-xl border border-emerald-800/40 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">KALKULATOR PINJAMAN</span>
                <h4 class="text-lg font-black mt-1">Estimasi Angsuran</h4>
                <p class="text-xs text-slate-300 mt-1">Simulasi real-time pengembalian dana</p>

                <div class="my-6 space-y-3 pt-4 border-t border-emerald-900/60 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Pokok Pinjaman:</span>
                        <span id="previewPokok" class="font-bold text-white">Rp 1.000.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jangka Waktu:</span>
                        <span id="previewTenor" class="font-bold text-white">6 Bulan</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Bunga Koperasi:</span>
                        <span class="font-bold text-emerald-400">1.0% / Bulan</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Bunga:</span>
                        <span id="previewBungaTotal" class="font-bold text-white">Rp 60.000</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-emerald-900/40 border border-emerald-500/30">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-emerald-300">Angsuran Tiap Bulan</span>
                    <h3 id="previewAngsuranBulan" class="text-2xl font-black text-white mt-0.5">Rp 176.667</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Pokok + Bunga bulanan</p>
                </div>
            </div>

            <div class="pt-4 border-t border-emerald-900/60 text-[11px] text-slate-400">
                Total Pengembalian: <span id="previewTotalBayar" class="font-bold text-emerald-300">Rp 1.060.000</span>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function setNominal(val) {
        document.getElementById('inputJumlah').value = val;
        calculateLoan();
    }

    function calculateLoan() {
        const jumlah = parseFloat(document.getElementById('inputJumlah').value) || 0;
        let tenor = 6;
        document.querySelectorAll('.tenor-radio').forEach(r => {
            if (r.checked) tenor = parseInt(r.value);
        });

        const bungaRate = 0.01; // 1%
        const totalBunga = jumlah * bungaRate * tenor;
        const totalBayar = jumlah + totalBunga;
        const angsuranPerBulan = Math.round(totalBayar / tenor);

        document.getElementById('previewPokok').innerText = 'Rp ' + jumlah.toLocaleString('id-ID');
        document.getElementById('previewTenor').innerText = tenor + ' Bulan';
        document.getElementById('previewBungaTotal').innerText = 'Rp ' + totalBunga.toLocaleString('id-ID');
        document.getElementById('previewAngsuranBulan').innerText = 'Rp ' + angsuranPerBulan.toLocaleString('id-ID');
        document.getElementById('previewTotalBayar').innerText = 'Rp ' + totalBayar.toLocaleString('id-ID');
    }

    document.getElementById('inputJumlah').addEventListener('input', calculateLoan);
    calculateLoan();
</script>
@endpush
@endsection
