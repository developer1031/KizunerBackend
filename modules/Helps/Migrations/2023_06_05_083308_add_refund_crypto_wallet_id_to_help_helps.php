<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundCryptoWalletIdToHelpHelps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_helps', function (Blueprint $table) {
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
        Schema::table('help_helps', function (Blueprint $table) {
            $table->dropColumn('refund_crypto_wallet_id');
        });
    }
}
