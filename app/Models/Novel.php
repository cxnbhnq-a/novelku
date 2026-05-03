<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib panggil ini

class Novel extends Model
{
    use HasFactory, HasUuids; // 2. Gunakan di sini

    // Izin kolom mana aja yang boleh diisi dari form
    protected $fillable = [
        'creator_id','title', 'slug', 'synopsis', 'cover_image', 'status'
    ];

    // Relasi balik: Novel ini milik siapa?
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function genres()
{
    return $this->belongsToMany(Genre::class, 'genre_novel');
}

}