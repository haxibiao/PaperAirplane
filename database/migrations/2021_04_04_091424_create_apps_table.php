<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment("创建用户的 ID");
            $table->foreign("user_id")->references('id')->on("users");
            $table->unsignedBigInteger('bot_id')->comment("Bot ID");
            $table->foreign("bot_id")->references('id')->on("bots");
            $table->integer("status")->default(1)->comment("状态  0:禁用，1:启用");
            $table->json("users")->comment("订阅消息的用户们");
            $table->uuid("sign")->comment("签名，用于调用接口时的身份认证");
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
        Schema::dropIfExists('apps');
    }
}
