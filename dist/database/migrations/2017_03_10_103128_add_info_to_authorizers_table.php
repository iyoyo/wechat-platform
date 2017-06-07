<?php

/*
 * add .styleci.yml
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoToAuthorizersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorizers', function (Blueprint $table) {
            $table->string('nick_name');
            $table->string('head_img');
            $table->string('user_name');
            $table->string('principal_name');
            $table->string('qrcode_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorizers', function (Blueprint $table) {
            $table->dropColumn('nick_name');
            $table->dropColumn('head_img');
            $table->dropColumn('user_name');
            $table->dropColumn('principal_name');
            $table->dropColumn('qrcode_url');
        });
    }
}
