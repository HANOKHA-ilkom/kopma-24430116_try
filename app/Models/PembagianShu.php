<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembagianShu extends Model
{
    use HasFactory;

    protected $table = 'pembagian_shus';

    protected $fillable = [
        'tahun_buku',
        'total_shu',
        'persen_jasa_modal',
        'persen_jasa_usaha',
        'status',
        'tanggal_pembagian',
    ];

    public function details()
    {
        return $this->hasMany(DetailShuAnggota::class);
    }
}
