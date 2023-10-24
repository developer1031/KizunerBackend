<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletCryptoWallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_crypto_wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('currency', 10)->nullable();
            $table->string('wallet_address', 100)->nullable();
            $table->string('memo', 100)->nullable();
            $table->string('wallet_id', 100)->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_crypto_wallets');
    }
}
