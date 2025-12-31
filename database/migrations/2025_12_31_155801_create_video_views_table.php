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
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->integer('video_id');
            $table->string('video_type', 50); // Movies, Episodes, Sports, LiveTV
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->timestamps();

            $table->index(['video_id', 'video_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
