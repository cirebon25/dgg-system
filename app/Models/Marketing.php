<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    protected $fillable = ['nama_marketing', 'no_telp', 'aktif'];

    public function visits()
    {
        return $this->hasMany(ProspectVisit::class);
    }
}
