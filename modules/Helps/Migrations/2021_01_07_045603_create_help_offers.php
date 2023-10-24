<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('help_id');
            $table->string('help_title')->nullable();
            $table->dateTimeTz('help_update')->nullable();
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
            $table->boolean('offer_remind')->nullable()->default(false);
            $table->boolean('review_remind')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_offers');
    }
}
