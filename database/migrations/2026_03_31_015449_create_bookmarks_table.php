<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('bookmarks', function (Blueprint $table) {
        $table->uuid('id')->primary(); // PK jadi UUID
        
        // Pake foreignUuid buat nyambung ke tabel users & novels yang PK-nya juga UUID
        $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignUuid('novel_id')->constrained('novels')->cascadeOnDelete();
        
        $table->timestamps();
        $table->unique(['user_id', 'novel_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
