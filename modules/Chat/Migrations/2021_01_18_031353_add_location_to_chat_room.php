<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToChatRoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->double('latitude', 100)->nullable();
            $table->double('longitude', 100)->nullable();
            $table->double('altitude', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('city');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('altitude');
        });
    }
}
