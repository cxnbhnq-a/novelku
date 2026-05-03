<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib panggil ini

class Chapter extends Model
{
    use HasFactory, HasUuids; // 2. Gunakan di sini

    // 3. Masukin kolom 'chapter_image' ke fillable (soalnya di Controller lu nyimpen ini)
    protected $fillable = [
        'novel_id', 'chapter_number', 'title', 'content', 'chapter_image'
    ];

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
}