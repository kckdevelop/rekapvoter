<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tps extends Model
{
    use HasFactory;

    protected $table = 'tps';

    protected $fillable = [
        'nama_tps',
        // Real Count Fields
        'suara_kandidat',
        'suara_lawan',
        'suara_tidak_sah',
        'is_submitted',
        'catatan_saksi',
        'waktu_input_real',
        // Quick Count Fields
        'quick_suara_kandidat',
        'quick_suara_lawan',
        'quick_suara_tidak_sah',
        'quick_is_submitted',
        'quick_catatan_saksi',
        'quick_waktu_input',
    ];

    protected $casts = [
        'suara_kandidat' => 'integer',
        'suara_lawan' => 'integer',
        'suara_tidak_sah' => 'integer',
        'is_submitted' => 'boolean',
        'waktu_input_real' => 'datetime',
        'quick_suara_kandidat' => 'integer',
        'quick_suara_lawan' => 'integer',
        'quick_suara_tidak_sah' => 'integer',
        'quick_is_submitted' => 'boolean',
        'quick_waktu_input' => 'datetime',
    ];

    /**
     * User/Saksi yang ditugaskan di TPS ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tps_id');
    }

    /**
     * Semua pemilih yang terdaftar di TPS ini.
     */
    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class, 'tps_id');
    }

    /**
     * Pemilih yang menjadi pendukung di TPS ini.
     */
    public function supporters(): HasMany
    {
        return $this->hasMany(Voter::class, 'tps_id')->where('is_supporter', true);
    }

    /**
     * Perolehan suara Real Count per kandidat di TPS ini.
     */
    public function candidateResults(): HasMany
    {
        return $this->hasMany(TpsCandidateResult::class, 'tps_id');
    }

    /**
     * Perolehan suara Quick Count per kandidat di TPS ini.
     */
    public function quickCandidateResults(): HasMany
    {
        return $this->hasMany(TpsQuickCandidateResult::class, 'tps_id');
    }

    /**
     * Total Suara Sah Real = Suara Kandidat Utama + Suara Lawan
     */
    public function getSuaraSahAttribute(): int
    {
        return ($this->suara_kandidat ?? 0) + ($this->suara_lawan ?? 0);
    }

    /**
     * Total Suara Masuk Real = Suara Sah + Suara Tidak Sah
     */
    public function getTotalSuaraMasukAttribute(): int
    {
        return $this->suara_sah + ($this->suara_tidak_sah ?? 0);
    }

    /**
     * Persentase Suara Real Kandidat dari Total Suara Sah
     */
    public function getPersentaseKemenanganAttribute(): float
    {
        if ($this->suara_sah <= 0) {
            return 0.0;
        }
        return round(($this->suara_kandidat / $this->suara_sah) * 100, 1);
    }

    /**
     * Total Suara Sah Quick Count
     */
    public function getQuickSuaraSahAttribute(): int
    {
        return ($this->quick_suara_kandidat ?? 0) + ($this->quick_suara_lawan ?? 0);
    }

    /**
     * Total Suara Masuk Quick Count (Hanya Suara Sah)
     */
    public function getQuickTotalSuaraMasukAttribute(): int
    {
        return $this->quick_suara_sah;
    }

    /**
     * Persentase Suara Quick Count Kandidat dari Total Suara Sah Quick Count
     */
    public function getQuickPersentaseKemenanganAttribute(): float
    {
        if ($this->quick_suara_sah <= 0) {
            return 0.0;
        }
        return round(($this->quick_suara_kandidat / $this->quick_suara_sah) * 100, 1);
    }
}
