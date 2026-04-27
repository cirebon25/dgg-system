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

    public function deployments()
    {
    return $this->hasMany(Deployment::class);
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function deployment() { return $this->hasOne(Deployment::class); }
}