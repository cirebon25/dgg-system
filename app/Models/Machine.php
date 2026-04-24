<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'serial_number',
        'tipe_model',
        'status',
        'keterangan_awal'
    ];
}