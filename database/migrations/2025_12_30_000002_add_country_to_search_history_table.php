<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryToSearchHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_history', function (Blueprint $table) {
            $table->string('country')->nullable()->after('ip_address');
            $table->string('country_code')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_history', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('country_code');
        });
    }
}
