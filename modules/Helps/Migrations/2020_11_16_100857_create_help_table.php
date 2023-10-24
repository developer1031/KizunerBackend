<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_helps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTimeTz('start')->nullable();
            $table->dateTimeTz('end')->nullable();
            $table->double('budget')->nullable();
            $table->uuid('user_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
            $table->index(['user_id', 'title']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_helps');
    }
}
