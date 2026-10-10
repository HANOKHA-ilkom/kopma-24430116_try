<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngsuranPinjaman extends Model
{
    use HasFactory;

    protected $table = 'angsuran_pinjamans';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'nominal_angsuran',
        'pokok',
        'bunga',
        'denda',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
        'keterangan',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
