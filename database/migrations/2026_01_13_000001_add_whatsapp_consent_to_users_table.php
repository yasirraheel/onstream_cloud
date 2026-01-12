<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappConsentToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp_consent')) {
                // Check if mobile column exists, if so put it after that, otherwise after email
                if (Schema::hasColumn('users', 'mobile')) {
                    $table->boolean('whatsapp_consent')->default(0)->after('mobile');
                } else {
                    $table->boolean('whatsapp_consent')->default(0)->after('email');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'whatsapp_consent')) {
                $table->dropColumn('whatsapp_consent');
            }
        });
    }
}
