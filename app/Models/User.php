<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tps_id',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tps_id' => 'integer',
        ];
    }

    /**
     * Relasi ke TPS yang ditugaskan kepada user ini.
     */
    public function tps()
    {
        return $this->belongsTo(Tps::class, 'tps_id');
    }

    /**
     * Cek apakah user adalah administrator.
     */
    public function isAdmin(): bool
    {
        return empty($this->role) || $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah saksi / petugas TPS.
     */
    public function isSaksi(): bool
    {
        return $this->role === 'saksi';
    }
}
