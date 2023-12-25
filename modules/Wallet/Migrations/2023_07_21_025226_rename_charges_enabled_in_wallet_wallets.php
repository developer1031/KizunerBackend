<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameChargesEnabledInWalletWallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet_wallets', function (Blueprint $table) {
            $table->renameColumn('charges_enabled', 'payouts_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_wallets', function (Blueprint $table) {
            // $table->renameColumn('charges_enabled', 'payouts_enabled');
            $table->dropColumn('payouts_enabled');
        });
    }
}
