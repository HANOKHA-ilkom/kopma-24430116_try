<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'nim',
        'program_studi',
        'fakultas',
        'angkatan',
        'status',
        'tanggal_bergabung',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simpanans()
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjamans()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}
