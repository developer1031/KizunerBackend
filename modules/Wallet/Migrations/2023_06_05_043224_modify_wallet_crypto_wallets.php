<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyWalletCryptoWallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('wallet_crypto_wallets', 'memo')) {
          Schema::table('wallet_crypto_wallets', function (Blueprint $table) {
              $table->renameColumn('memo', 'extra_id');
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

        if (Schema::hasColumn('wallet_crypto_wallets', 'extra_id')) {
            Schema::table('wallet_crypto_wallets', function (Blueprint $table) {
                $table->renameColumn('extra_id', 'memo');
            });
        }
    }
}
