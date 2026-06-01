<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class TypeModel extends Model
{
    use HasFactory, UppercaseAttributes;

    protected $table = 'type_models';

    protected $fillable = [
        'nama_tipe',
    ];
}
