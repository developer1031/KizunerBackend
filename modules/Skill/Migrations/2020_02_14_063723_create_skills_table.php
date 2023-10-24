<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('admin')->default(false);
            $table->boolean('suggest')->default(false);
            $table->integer('count')->nullable()->default(0);
            $table->timestampsTz();
        });

        Schema::create('skillables', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('skill_id');
            $table->uuid('skillable_id');
            $table->string('skillable_type');
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
        Schema::dropIfExists('skills');
    }
}
