@extends('layouts.app')

@section('title', 'Katalog Produk & Stok Toko')
@section('page_title', 'Manajemen Inventaris Toko KOPMA')
@section('page_subtitle', 'Katalog barang, harga, dan kontrol stok minimarket')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Jenis Produk</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalItem) }} Item</h3>
            <p class="text-[11px] text-slate-400 mt-1">Terdaftar di katalog</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Stok Fisik</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalStok) }} Pcs</h3>
            <p class="text-[11px] text-slate-400 mt-1">Tersedia untuk dijual</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-amber-300 bg-amber-50/50 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Peringatan Stok Menipis</span>
            <h3 class="text-2xl font-black text-amber-950 mt-1">{{ number_format($lowStockCount) }} Produk</h3>
            <p class="text-[11px] text-amber-700 font-semibold mt-1">Stok &le; Stok Minimal</p>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('produks.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode produk..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <select name="kategori_id" onchange="this.form.submit()" class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('kategori_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->nama }} ({{ $c->produks_count }})
                </option>
                @endforeach
            </select>
        </form>

        @if(Auth::user()->role !== 'anggota')
        <button type="button" onclick="document.getElementById('modalAddProduct').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex-shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Produk Baru</span>
        </button>
        @endif
    </div>

    <!-- Products Table -->
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama Produk</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Harga Beli</th>
                        <th class="py-3.5 px-4">Harga Jual</th>
                        <th class="py-3.5 px-4">Stok</th>
                        <th class="py-3.5 px-4">Status Stok</th>
                        @if(Auth::user()->role !== 'anggota')
                        <th class="py-3.5 px-4 text-right">Kelola Stok</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($produks as $p)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $p->kode_produk }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $p->nama_produk }}</td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $p->kategori->nama ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-slate-500">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 font-extrabold text-emerald-700">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $p->stok }} {{ $p->satuan }}</td>
                        <td class="py-3.5 px-4">
                            @if($p->stok <= 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                                Habis
                            </span>
                            @elseif($p->stok <= $p->stok_minimal)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                Menipis (&le; {{ $p->stok_minimal }})
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Aman
                            </span>
                            @endif
                        </td>
                        @if(Auth::user()->role !== 'anggota')
                        <td class="py-3.5 px-4 text-right">
                            <form action="{{ route('produks.stok', $p->id) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                <input type="number" name="jumlah" value="5" min="1" class="w-14 px-2 py-1 rounded-lg bg-slate-100 border border-slate-200 text-center text-xs font-bold">
                                <button type="submit" name="tipe" value="tambah" class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs" title="Tambah Stok">
                                    +
                                </button>
                                <button type="submit" name="tipe" value="kurang" class="px-2 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs" title="Kurang Stok">
                                    -
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">Belum ada data produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $produks->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Produk Baru -->
<div id="modalAddProduct" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h4 class="text-base font-bold text-slate-900">Tambah Produk Baru ke Katalog</h4>
            <button type="button" onclick="document.getElementById('modalAddProduct').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                ✕
            </button>
        </div>

        <form action="{{ route('produks.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Produk *</label>
                <input type="text" name="nama_produk" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: Pulpen Gel 0.5mm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori *</label>
                    <select name="kategori_produk_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Satuan *</label>
                    <input type="text" name="satuan" value="pcs" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none" placeholder="pcs/botol/pack">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga Beli / Modal *</label>
                    <input type="number" name="harga_beli" min="0" step="100" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none" placeholder="3000">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga Jual *</label>
                    <input type="number" name="harga_jual" min="0" step="100" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-emerald-700 focus:bg-white focus:outline-none" placeholder="4500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok Awal *</label>
                    <input type="number" name="stok" min="0" value="10" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok Minimal *</label>
                    <input type="number" name="stok_minimal" min="1" value="5" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalAddProduct').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
