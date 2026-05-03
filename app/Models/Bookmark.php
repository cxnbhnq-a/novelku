<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib panggil ini

class Bookmark extends Model
{
    use HasUuids; // 2. Gunakan di sini

    protected $fillable = ['user_id', 'novel_id'];

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
}