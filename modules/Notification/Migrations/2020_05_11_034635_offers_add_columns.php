<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OffersAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_offers', function(Blueprint $table) {
            $table->boolean('offer_remind')->nullable()->default(false);
            $table->boolean('review_remind')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hangout_offers', function(Blueprint $table) {
            $table->dropColumn('offer_remind');
            $table->dropColumn('review_remind');
        });
    }
}
