<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersAddBlockColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->boolean('admin')->default(false)->nullable();
            $table->boolean('block')->default(false)->nullable();
            $table->boolean('deleted')->nullable();
            $table->string('deleted_email')->nullable();
            $table->string('deleted_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('admin');
            $table->dropColumn('block');
            $table->dropColumn('deleted');
            $table->dropColumn('deleted_email');
            $table->dropColumn('deleted_phone');
        });
    }
}
