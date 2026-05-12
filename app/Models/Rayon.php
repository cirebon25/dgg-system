<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rayon extends Model
{
    use HasFactory;

    protected $fillable = ['nama_rayon'];

    // ✨ TAMBAHKAN INI: Fungsi agar Rayon bisa memanggil daftar Customernya
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function technician()
    {
    return $this->belongsTo(Technician::class);
    }
}
