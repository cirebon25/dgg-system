<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartBorrowingHeader extends Model
{
    protected $guarded = [];

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function items()
    {
        return $this->hasMany(PartBorrowingItem::class);
    }
}