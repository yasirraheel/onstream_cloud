<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdClickLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_click_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->index();
            $table->timestamp('clicked_at')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_click_logs');
    }
}
