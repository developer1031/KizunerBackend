<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundCryptoWalletIdToHangoutOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_offers', function (Blueprint $table) {
            $table->string('refund_crypto_wallet_id')->nullable();
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
            $table->dropColumn('refund_crypto_wallet_id');
        });
    }
}
