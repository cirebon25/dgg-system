<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeModel extends Model
{
    use HasFactory;

    protected $table = 'type_models';

    protected $fillable = [
        'nama_tipe',
    ];
}