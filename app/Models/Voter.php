<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voter extends Model
{
    use HasFactory;

    protected $fillable = ['tps_id', 'nama', 'is_supporter'];

    protected $casts = [
        'is_supporter' => 'boolean',
    ];

    /**
     * TPS tempat pemilih ini terdaftar.
     */
    public function tps(): BelongsTo
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }
}
