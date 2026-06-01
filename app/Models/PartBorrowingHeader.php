<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class PartBorrowingHeader extends Model
{
    use UppercaseAttributes;
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
