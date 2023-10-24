<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            /** Basic Information */
            $table->string('name')->nullable();
            $table->text('about')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->integer('gender')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('social')->nullable();
            /** Security */
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('social_provider')->nullable();
            $table->string('social_id')->nullable();
            $table->string('social_avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index(['name', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
