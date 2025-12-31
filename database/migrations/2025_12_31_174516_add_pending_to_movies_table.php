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
        Schema::table('movie_videos', function (Blueprint $table) {
            $table->boolean('pending')->default(0)->after('upcoming')->comment('0=No, 1=Yes');
            $table->index('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_videos', function (Blueprint $table) {
            $table->dropColumn('pending');
        });
    }
};
