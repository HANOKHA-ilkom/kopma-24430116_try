<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjamans';

    protected $fillable = [
        'member_id',
        'kode_pinjaman',
        'jumlah_pinjaman',
        'bunga_persen',
        'tenor_bulan',
        'angsuran_per_bulan',
        'sisa_pinjaman',
        'status',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'approved_by',
        'alasan_ditolak',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function angsurans()
    {
        return $this->hasMany(AngsuranPinjaman::class);
    }
}
