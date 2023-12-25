<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');//Who rate
            $table->integer('rate');
            $table->text('comment')->nullable();
            $table->uuid('ratted_user_id');
            $table->uuid('offer_id');
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
        Schema::dropIfExists('rating_ratings');
    }
}
