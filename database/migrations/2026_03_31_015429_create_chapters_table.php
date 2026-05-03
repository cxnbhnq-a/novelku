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
    Schema::create('chapters', function (Blueprint $table) {
        $table->uuid('id')->primary(); // PK jadi UUID
        
        $table->foreignUuid('novel_id')->constrained('novels')->cascadeOnDelete();
        $table->integer('chapter_number');
        $table->string('title');
        $table->longText('content');
        $table->string('chapter_image')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
