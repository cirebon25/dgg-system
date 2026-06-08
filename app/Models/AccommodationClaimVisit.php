<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\UppercaseAttributes;

class AccommodationClaimVisit extends Model
{
    use HasFactory, UppercaseAttributes;

    protected $fillable = [
        'accommodation_claim_id',
        'no_urut',
        'nama_customer',
        'alamat',
        'keterangan',
    ];

    public function customer()
    {
    return $this->belongsTo(\App\Models\Customer::class);
    }
}
