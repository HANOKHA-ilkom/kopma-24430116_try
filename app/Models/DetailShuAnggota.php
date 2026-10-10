<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailShuAnggota extends Model
{
    use HasFactory;

    protected $table = 'detail_shu_anggotas';

    protected $fillable = [
        'pembagian_shu_id',
        'member_id',
        'shu_jasa_modal',
        'shu_jasa_usaha',
        'total_shu_diterima',
        'status_pencairan',
        'tanggal_pencairan',
    ];

    public function pembagianShu()
    {
        return $this->belongsTo(PembagianShu::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
