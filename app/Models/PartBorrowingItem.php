<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class PartBorrowingItem extends Model
{
    use UppercaseAttributes;
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
