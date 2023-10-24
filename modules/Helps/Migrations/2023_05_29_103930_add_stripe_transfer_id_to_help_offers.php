<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeTransferIdToHelpOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_offers', function (Blueprint $table) {
            $table->string('stripe_transfer_id')->nullable();
            $table->string('now_payments_transfer_id')->nullable();
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
            $table->dropColumn('stripe_transfer_id');
            $table->dropColumn('now_payments_transfer_id');
        });
    }
}
