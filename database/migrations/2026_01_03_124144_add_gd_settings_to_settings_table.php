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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('gd_api_key')->nullable()->after('tmdb_api_language');
            $table->text('gd_folder_ids')->nullable()->after('gd_api_key');
            $table->timestamp('gd_last_fetch_at')->nullable()->after('gd_folder_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['gd_api_key', 'gd_folder_ids', 'gd_last_fetch_at']);
        });
    }
};
