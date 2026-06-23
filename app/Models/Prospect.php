<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'kota',
        'alamat',
        'pic_nama',
        'pic_jabatan',
        'pic_telp',
        'status',
    ];

    public function visits()
    {
        return $this->hasMany(ProspectVisit::class)->orderBy('tanggal_kunjungan', 'desc');
    }

    public function lastVisit()
    {
        return $this->hasOne(ProspectVisit::class)->latestOfMany('tanggal_kunjungan');
    }
}
