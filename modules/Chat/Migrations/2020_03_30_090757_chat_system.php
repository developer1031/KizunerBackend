<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChatSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('type');
            $table->string('status');
            $table->index('id');
            $table->index('type');
            $table->index('status');
            $table->timestampsTz();
        });

        Schema::create('chat_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('user_id');
            $table->boolean('owner')->default(false);
            $table->index('room_id');
            $table->index('user_id');
            $table->timestampsTz();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('user_id');
            $table->string('text')->nullable();
            $table->string('hangout')->nullable();
            $table->index('room_id');
            $table->index('user_id');
            $table->timestampsTz();
        });

        Schema::create('chat_message_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id')->nullable();
            $table->text('original');
            $table->text('thumb');
            $table->timestampsTz();
        });

        Schema::create('chat_user_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->boolean('status');
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
        Schema::drop('chat_message_images');
        Schema::drop('chat_messages');
        Schema::drop('chat_group_members');
        Schema::drop('chat_rooms');
    }
}
