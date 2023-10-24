<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefferalCodeToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('referer_user_id')->unsigned()->nullable();
            $table->integer('referer_company_id')->unsigned()->nullable();
            $table->string('referal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referer_user_id');
            $table->dropColumn('referer_company_id');
            $table->dropColumn('referal_code');
        });
    }
}
