<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectVisit extends Model
{
    protected $fillable = [
        'prospect_id',
        'marketing_id',
        'tanggal_kunjungan',
        'jenis_mesin_existing',
        'merk_mesin_existing',
        'hasil_kunjungan',
        'catatan',
    ];

    protected $casts = ['tanggal_kunjungan' => 'date'];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class);
    }
}
