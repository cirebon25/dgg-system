<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentSparepart extends Model
{
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

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}