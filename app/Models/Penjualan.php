<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'kode_transaksi',
        'member_id',
        'kasir_id',
        'subtotal',
        'diskon',
        'total_akhir',
        'bayar',
        'kembali',
        'metode_pembayaran',
        'status_pembayaran',
        'tanggal_transaksi',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}
