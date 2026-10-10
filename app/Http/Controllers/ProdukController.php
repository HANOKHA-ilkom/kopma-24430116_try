<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $categories = KategoriProduk::withCount('produks')->get();

        $query = Produk::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_produk_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        $produks = $query->paginate(12)->withQueryString();

        $totalItem = Produk::count();
        $totalStok = Produk::sum('stok');
        $lowStockCount = Produk::whereColumn('stok', '<=', 'stok_minimal')->count();

        return view('produks.index', compact(
            'produks',
            'categories',
            'totalItem',
            'totalStok',
            'lowStockCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
        ]);

        $kode = 'PRD-' . strtoupper(Str::random(5));

        Produk::create([
            'kategori_produk_id' => $request->kategori_produk_id,
            'kode_produk' => $kode,
            'nama_produk' => $request->nama_produk,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimal' => $request->stok_minimal,
            'satuan' => $request->satuan,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        return back()->with('success', "Produk baru ({$request->nama_produk}) berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        $produk->update($request->only([
            'nama_produk',
            'kategori_produk_id',
            'harga_beli',
            'harga_jual',
            'stok_minimal',
            'satuan',
        ]));

        return back()->with('success', "Data produk {$produk->nama_produk} berhasil diupdate.");
    }

    public function updateStok(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'jumlah' => 'required|integer|not_in:0',
            'tipe' => 'required|in:tambah,kurang',
        ]);

        if ($request->tipe === 'tambah') {
            $produk->increment('stok', $request->jumlah);
        } else {
            if ($produk->stok < $request->jumlah) {
                return back()->with('error', 'Stok saat ini tidak mencukupi untuk pengurangan ini.');
            }
            $produk->decrement('stok', $request->jumlah);
        }

        return back()->with('success', "Stok produk {$produk->nama_produk} berhasil diperbarui.");
    }
}
