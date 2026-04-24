<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    protected $fillable = ['rayon_id', 'nama_technician', 'nomor_hp', 'is_active'];
    public function rayon()
{
    return $this->belongsTo(Rayon::class);
}
}