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
        Schema::create('api_urls', function (Blueprint $table) {
            $table->id();
            $table->string('movie_name')->nullable();
            $table->text('url');
            $table->boolean('is_used')->default(0); // 0 = Available, 1 = Used
            $table->timestamps();
            
            // Add index for faster searching
            $table->index('is_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_urls');
    }
};
