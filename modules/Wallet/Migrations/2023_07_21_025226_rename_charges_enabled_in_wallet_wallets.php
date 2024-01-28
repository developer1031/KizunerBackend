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
        if (Schema::hasColumn('wallet_wallets', 'charges_enabled')) {
          Schema::table('wallet_wallets', function (Blueprint $table) {
              $table->renameColumn('charges_enabled', 'payouts_enabled');
          });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('wallet_wallets', 'payouts_enabled')) {
          Schema::table('wallet_wallets', function (Blueprint $table) {
              $table->renameColumn('payouts_enabled', 'charges_enabled');
              // $table->dropColumn('payouts_enabled');
          });
        }
    }
}
