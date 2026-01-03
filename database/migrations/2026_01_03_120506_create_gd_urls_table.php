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
        Schema::create('gd_urls', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->nullable();
            $table->text('url');
            $table->string('file_id')->nullable(); // Google Drive file ID
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable(); // File MIME type
            $table->boolean('is_used')->default(0); // 0 = Available, 1 = Used
            $table->timestamps();
            
            // Add indexes for faster searching
            $table->index('is_used');
            $table->index('file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gd_urls');
    }
};
