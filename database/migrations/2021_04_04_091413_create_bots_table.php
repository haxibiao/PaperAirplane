<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign("user_id")->references('id')->on("users");
            $table->string("fs_app_id")->unique()->comment("飞书 APP ID");
            $table->string("fs_app_secret")->comment("飞书 APP secret");
            $table->string("fs_access_token")->comment("飞书 token 用于调用飞书 API");
            $table->integer("fs_access_time")->comment("飞书 token 失效时间");
            $table->string("remarks")->comment("备注");
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
        Schema::dropIfExists('bots');
    }
}
