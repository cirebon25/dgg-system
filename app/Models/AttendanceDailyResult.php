<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDailyResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tgl_shift'   => 'date',
        'scan_in'     => 'datetime',
        'scan_out'    => 'datetime',
        'libur_umum'  => 'boolean',
        'libur_rutin' => 'boolean',
        'jk_ot'       => 'boolean',
    ];
}