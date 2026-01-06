<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAnnouncementsTable extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('image')->nullable()->after('view_count');
            $table->string('cta_text')->nullable()->after('image');
            $table->string('cta_url')->nullable()->after('cta_text');
            $table->string('cta_target')->nullable()->default('_self')->after('cta_url');
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['image', 'cta_text', 'cta_url', 'cta_target']);
        });
    }
}

