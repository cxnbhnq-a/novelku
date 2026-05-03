<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib panggil ini

class Genre extends Model
{
    use HasFactory, HasUuids; // 2. Gunakan di sini

    protected $fillable = ['name', 'slug'];

    // Relasi Many-to-Many ke Novel
    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'genre_novel');
    }
}