<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    // Kolom yang boleh diisi
    protected $fillable = [
        'name', 'email', 'password',
    ];

    // Sembunyikan password
    protected $hidden = [
        'password',
    ];
}