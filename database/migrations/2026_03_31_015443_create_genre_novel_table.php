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
    Schema::create('genre_novel', function (Blueprint $table) {
        $table->foreignUuid('novel_id')->constrained('novels')->cascadeOnDelete();
        $table->foreignUuid('genre_id')->constrained('genres')->cascadeOnDelete();
        $table->primary(['novel_id', 'genre_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_novel');
    }
};
