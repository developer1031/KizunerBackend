<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHangoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hangout_hangouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTimeTz('start')->nullable();
            $table->dateTimeTz('end')->nullable();
            $table->double('kizuna')->nullable();
            $table->uuid('user_id')->nullable();
            $table->integer('type')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('available')->nullable();
            $table->string('schedule')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
            $table->index(['user_id', 'title']);
        });

        Schema::create('hangout_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('hangout_id');
            $table->string('hangout_title')->nullable();
            $table->dateTimeTz('hangout_update')->nullable();
            $table->uuid('sender_id');
            $table->uuid('receiver_id');
            $table->integer('kizuna')->nullable();
            $table->integer('position')->nullable();
            $table->dateTimeTz('start')->nullable();
            $table->dateTimeTz('end')->nullable();
            $table->text('address')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hangout_offers');
        Schema::dropIfExists('hangout_hangouts');
    }
}
