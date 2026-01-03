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
        Schema::table('gd_urls', function (Blueprint $table) {
            $table->string('folder_id')->nullable()->after('file_id'); // Google Drive folder ID
            $table->index('folder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gd_urls', function (Blueprint $table) {
            $table->dropIndex(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
