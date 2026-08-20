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
        'suara_kandidat',
        'suara_lawan',
        'suara_tidak_sah',
        'is_submitted',
        'catatan_saksi',
        'waktu_input_real',
    ];

    protected $casts = [
        'suara_kandidat' => 'integer',
        'suara_lawan' => 'integer',
        'suara_tidak_sah' => 'integer',
        'is_submitted' => 'boolean',
        'waktu_input_real' => 'datetime',
    ];

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
     * Perolehan suara per kandidat di TPS ini.
     */
    public function candidateResults(): HasMany
    {
        return $this->hasMany(TpsCandidateResult::class, 'tps_id');
    }

    /**
     * Total Suara Sah = Suara Kandidat Utama + Suara Lawan
     */
    public function getSuaraSahAttribute(): int
    {
        return ($this->suara_kandidat ?? 0) + ($this->suara_lawan ?? 0);
    }

    /**
     * Total Suara Masuk = Suara Sah + Suara Tidak Sah
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
}
