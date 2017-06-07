<?php

/*
 * add .styleci.yml
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorizersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorizers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appid', 100)->unique()->comment('AppId');
            $table->string('access_token', 500)->nullable()->comment('调用凭据');
            $table->string('refresh_token', 500)->nullable()->comment('刷新凭据');
            $table->text('func_info')->nullable()->comment('授权接口');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('authorizers');
    }
}
