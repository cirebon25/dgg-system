<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartRo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sparepartUsageRos()
    {
        return $this->hasMany(SparepartUsageRo::class);
    }
}
