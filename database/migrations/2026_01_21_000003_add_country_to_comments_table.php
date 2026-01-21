<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('comments') && !Schema::hasColumn('comments', 'country')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->string('country')->nullable()->after('comment');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('comments') && Schema::hasColumn('comments', 'country')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }
}
