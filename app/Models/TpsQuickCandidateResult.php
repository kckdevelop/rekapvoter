<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TpsQuickCandidateResult extends Model
{
    use HasFactory;

    protected $table = 'tps_quick_candidate_results';

    protected $fillable = [
        'tps_id',
        'candidate_id',
        'jumlah_suara',
    ];

    protected $casts = [
        'jumlah_suara' => 'integer',
    ];

    public function tps(): BelongsTo
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}
