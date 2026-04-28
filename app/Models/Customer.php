<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory; 

    protected $fillable = ['rayon_id', 'nama_customer', 'kota', 'alamat', 'nomor_telp'];

    // Menjelaskan bahwa Customer milik sebuah Rayon
    public function rayon()
    {
        return $this->belongsTo(Rayon::class);
    }

    public function deployments()
    {
    return $this->hasMany(Deployment::class);
    }
}