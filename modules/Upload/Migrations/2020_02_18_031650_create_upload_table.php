<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('uploadable_id')->nullable();
            $table->string('uploadable_type')->nullable();
            $table->text('path');
            $table->text('thumb')->nullable();
            $table->string('type')->nullable();
            $table->timestampsTz();
            $table->index(['uploadable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
    }
}
