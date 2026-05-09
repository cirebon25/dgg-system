<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineModel extends Model
{
    use HasFactory; // <--- WAJIB ADA INI

    protected $fillable = ['nama_model', 'brand'];
}
