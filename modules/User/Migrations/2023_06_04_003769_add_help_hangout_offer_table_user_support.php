<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHelpHangoutOfferTableUserSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_support', function (Blueprint $table) {
            $table->string('help_offer_id')->nullable();
            $table->string('hangout_offer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_support', function (Blueprint $table) {
            $table->dropColumn('help_offer_id');
            $table->dropColumn('hangout_offer_id');
        });
    }
}
