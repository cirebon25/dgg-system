<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceShift extends Model
{
    protected $primaryKey = 'jk_id';

    public $incrementing = false;

    protected $guarded = [];
}