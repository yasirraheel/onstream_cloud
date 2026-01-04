<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiUrlSettingsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('api_url_base_url')->nullable()->after('gd_last_fetch_at');
            $table->string('api_url_api_key')->nullable()->after('api_url_base_url');
            $table->timestamp('api_url_last_fetch_at')->nullable()->after('api_url_api_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['api_url_base_url', 'api_url_api_key', 'api_url_last_fetch_at']);
        });
    }
}
