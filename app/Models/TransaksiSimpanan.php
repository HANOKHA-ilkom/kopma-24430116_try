<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiSimpanan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_simpanans';

    protected $fillable = [
        'simpanan_id',
        'kode_transaksi',
        'jenis_transaksi',
        'jumlah',
        'tanggal_transaksi',
        'keterangan',
        'created_by',
    ];

    public function simpanan()
    {
        return $this->belongsTo(Simpanan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
