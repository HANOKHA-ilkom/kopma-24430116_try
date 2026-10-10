<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Member;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Simpanan;
use App\Models\TransaksiSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = KategoriProduk::all();
        $members = Member::with('user')->where('status', 'aktif')->get();

        $query = Produk::where('is_active', true)->where('stok', '>', 0);

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

        $produks = $query->get();

        return view('pos.index', compact('categories', 'members', 'produks'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'member_id' => 'nullable|exists:members,id',
            'metode_pembayaran' => 'required|in:tunai,qris,saldo_kopma,transfer',
            'bayar' => 'required|numeric|min:0',
        ]);

        $memberId = $request->member_id;
        $items = $request->items;
        $metode = $request->metode_pembayaran;

        return DB::transaction(function () use ($request, $items, $memberId, $metode) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['id']);
                if ($produk->stok < $item['qty']) {
                    return back()->with('error', "Stok untuk {$produk->nama_produk} tidak mencukupi (Tersisa: {$produk->stok}).");
                }

                $itemSubtotal = $produk->harga_jual * $item['qty'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'harga' => $produk->harga_jual,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Diskon Anggota 5% jika pembeli adalah anggota terdaftar
            $diskon = $memberId ? round($subtotal * 0.05) : 0;
            $totalAkhir = $subtotal - $diskon;
            $bayar = $request->bayar;

            if ($metode === 'saldo_kopma') {
                if (!$memberId) {
                    return back()->with('error', 'Pembayaran via Saldo KOPMA hanya dapat digunakan oleh Anggota terdaftar.');
                }
                $simpananSukarela = Simpanan::where('member_id', $memberId)
                    ->whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))
                    ->first();

                if (!$simpananSukarela || $simpananSukarela->saldo < $totalAkhir) {
                    return back()->with('error', 'Saldo Simpanan Sukarela Anggota tidak mencukupi.');
                }

                $simpananSukarela->decrement('saldo', $totalAkhir);
                $bayar = $totalAkhir;

                TransaksiSimpanan::create([
                    'simpanan_id' => $simpananSukarela->id,
                    'kode_transaksi' => 'TRX-POS-' . strtoupper(Str::random(6)),
                    'jenis_transaksi' => 'penarikan',
                    'jumlah' => $totalAkhir,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Belanja di Kasir KOPMA (Debit Saldo)',
                    'created_by' => Auth::id(),
                ]);
            }

            if ($bayar < $totalAkhir) {
                return back()->with('error', 'Nominal uang pembayaran kurang dari total belanja.');
            }

            $kembali = $bayar - $totalAkhir;

            $penjualan = Penjualan::create([
                'kode_transaksi' => 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'member_id' => $memberId,
                'kasir_id' => Auth::id(),
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_akhir' => $totalAkhir,
                'bayar' => $bayar,
                'kembali' => $kembali,
                'metode_pembayaran' => $metode,
                'status_pembayaran' => 'lunas',
                'tanggal_transaksi' => now(),
            ]);

            foreach ($orderItems as $oi) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $oi['produk']->id,
                    'harga_satuan' => $oi['harga'],
                    'jumlah' => $oi['qty'],
                    'subtotal' => $oi['subtotal'],
                ]);

                $oi['produk']->decrement('stok', $oi['qty']);
            }

            return redirect()->route('pos.struk', $penjualan->id)
                ->with('success', 'Transaksi berhasil disimpan!');
        });
    }

    public function struk($id)
    {
        $penjualan = Penjualan::with(['details.produk', 'member.user', 'kasir'])->findOrFail($id);
        return view('pos.struk', compact('penjualan'));
    }
}
