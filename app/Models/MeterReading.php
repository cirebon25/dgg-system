<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterReading extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'machine_id',
        'customer_id',
        'technician_id',
        'tanggal',
        'counter_bw_lalu',
        'counter_bw',
        'usage_bw',
        'counter_color_lalu',
        'counter_color',
        'usage_color',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }
}
