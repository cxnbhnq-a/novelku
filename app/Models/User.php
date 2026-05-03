<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib panggil ini

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'otp_code',          // <-- TAMBAHIN INI BIAR BISA DISIMPAN
        'otp_expires_at',    // <-- TAMBAHIN INI JUGA
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function novels()
    {
        // Karena di tabel novels lu pake nama 'creator_id', 
        // kita wajib ngasih tau Laravel di parameter kedua.
        return $this->hasMany(Novel::class, 'creator_id');
    }
}
