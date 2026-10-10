<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'kategori_produk_id',
        'kode_produk',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimal',
        'satuan',
        'deskripsi',
        'foto',
        'is_active',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}
