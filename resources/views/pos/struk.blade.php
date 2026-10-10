@extends('layouts.app')

@section('title', 'Struk Transaksi - ' . $penjualan->kode_transaksi)
@section('page_title', 'Struk Bukti Pembayaran')
@section('page_subtitle', 'Transaksi Kasir POS Toko KOPMA')

@section('content')
<div class="max-w-md mx-auto">
    <!-- Receipt Box -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xl font-mono text-xs text-slate-800" id="receiptPrintArea">
        <!-- Receipt Header -->
        <div class="text-center pb-4 border-b border-dashed border-slate-300">
            <h3 class="text-base font-black tracking-wider uppercase">KOPERASI MAHASISWA (KOPMA)</h3>
            <p class="text-[11px] text-slate-500 font-sans mt-0.5">Unit Usaha Minimarket & Stationery</p>
            <p class="text-[10px] text-slate-400 font-sans">Gedung Student Center Kampus</p>
        </div>

        <!-- Meta Info -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span>No. Struk:</span>
                <span class="font-bold">{{ $penjualan->kode_transaksi }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu:</span>
                <span>{{ date('d/m/Y H:i', strtotime($penjualan->tanggal_transaksi)) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span>{{ $penjualan->kasir->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span class="font-bold">{{ $penjualan->member ? $penjualan->member->user->name . ' (Anggota)' : 'Umum' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-2">
            @foreach($penjualan->details as $d)
            <div>
                <div class="flex justify-between font-bold">
                    <span>{{ $d->produk->nama_produk }}</span>
                    <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="text-[10px] text-slate-400">
                    {{ $d->jumlah }} x Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="py-3 border-b border-dashed border-slate-300 space-y-1.5 text-[11px]">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
            <div class="flex justify-between text-emerald-700 font-bold">
                <span>Diskon Anggota (5%):</span>
                <span>- Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm font-black pt-1 border-t border-slate-200">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-1">
                <span>Bayar ({{ strtoupper($penjualan->metode_pembayaran) }}):</span>
                <span>Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kembali:</span>
                <span>Rp {{ number_format($penjualan->kembali, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Receipt Footer -->
        <div class="text-center pt-4 text-[10px] text-slate-400 space-y-1 font-sans">
            <p class="font-semibold text-slate-600">Terima kasih telah berbelanja di KOPMA!</p>
            <p>Keuntungan koperasi kembali kepada kesejahteraan anggota.</p>
        </div>
    </div>

    <!-- Action Buttons (Non-print) -->
    <div class="mt-4 flex items-center justify-between gap-3">
        <a href="{{ route('pos.index') }}" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs text-center transition-all flex items-center justify-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Transaksi Baru</span>
        </a>
        <button type="button" onclick="window.print()" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center gap-1.5">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak Struk</span>
        </button>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receiptPrintArea, #receiptPrintArea * {
            visibility: visible;
        }
        #receiptPrintArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection
