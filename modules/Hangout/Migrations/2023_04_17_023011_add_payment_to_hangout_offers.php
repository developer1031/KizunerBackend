<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentToHangoutOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_offers', function (Blueprint $table) {
            $table->double('amount')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->string('stripe_intent_id')->nullable();
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
            $table->dropColumn('amount');
            $table->dropColumn('payment_method');
            $table->dropColumn('stripe_intent_id');
        });
    }
}
