<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartBorrowingItem extends Model
{
    protected $guarded = [];

    public function header()
    {
        return $this->belongsTo(PartBorrowingHeader::class, 'part_borrowing_header_id');
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}