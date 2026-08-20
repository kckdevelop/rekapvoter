<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_urut',
        'nama',
        'is_main_candidate',
        'warna_badge',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
        'is_main_candidate' => 'boolean',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(TpsCandidateResult::class, 'candidate_id');
    }
}
