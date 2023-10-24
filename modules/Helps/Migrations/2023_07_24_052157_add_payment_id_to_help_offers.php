<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentIdToHelpOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_offers', function (Blueprint $table) {
            $table->string('invoice_url')->nullable();
            $table->string('stripe_intent_id')->nullable();
            $table->string('now_payments_id')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->string('now_payments_refund_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_offers', function (Blueprint $table) {
            $table->dropColumn('invoice_url');
            $table->dropColumn('stripe_intent_id');
            $table->dropColumn('now_payments_id');
            $table->dropColumn('stripe_refund_id');
            $table->dropColumn('now_payments_refund_id');
        });
    }
}
