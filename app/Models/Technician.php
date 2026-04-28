<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Technician extends Model
{
    use HasFactory; 
    
    protected $fillable = ['rayon_id', 'nama_technician', 'nomor_hp', 'is_active'];
    public function rayon()
{
    return $this->belongsTo(Rayon::class);
}
}