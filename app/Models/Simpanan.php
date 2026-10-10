<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'simpanans';

    protected $fillable = [
        'member_id',
        'jenis_simpanan_id',
        'nomor_rekening',
        'saldo',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class);
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiSimpanan::class);
    }
}
