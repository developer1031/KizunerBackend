<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('admin')->default(false);
            $table->boolean('suggest')->default(false);
            $table->integer('count')->nullable()->default(0);
            $table->timestampsTz();
        });

        Schema::create('categoryables', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('category_id');
            $table->uuid('categoryable_id');
            $table->string('categoryable_type');
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
        Schema::dropIfExists('categories');
    }
}
