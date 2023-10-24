<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelVariablesToHangoutOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_offers', function (Blueprint $table) {
            $table->integer('is_within_time')->nullable();
            $table->string('subject_cancel',65535)->nullable();
            $table->string('message_cancel',65535)->nullable();
            $table->integer('is_able_contact')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hangout_offers', function (Blueprint $table) {
            $table->dropColumn('is_within_time');
            $table->dropColumn('is_able_contact');
            $table->dropColumn('subject_cancel');
            $table->dropColumn('message_cancel');
        });
    }
}
