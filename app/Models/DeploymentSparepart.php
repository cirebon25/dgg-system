<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentSparepart extends Model
{
    use UppercaseAttributes;
    protected $table = 'deployment_sparepart'; // Nama tabel pivot Akang

    protected $fillable = [
        'deployment_id',
        'sparepart_id',
        'jumlah',
    ];

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }
}
