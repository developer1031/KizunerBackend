<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('stripe_id');
            $table->double('balance')->nullable();
            $table->double('available')->nullable();
            $table->timestampsTz();
        });

        Schema::create('wallet_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->string('name')->nullable();
            $table->string('payment_method');
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->boolean('default')->default(false);
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('wallet_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stripe_intent_id');//Get payment detail
            $table->uuid('wallet_id');
            $table->uuid('package_id');
            $table->uuid('card_id');
            $table->double('amount');
            $table->double('point');
            $table->timestampsTz();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sender');
            $table->uuid('receiver');
            $table->double('point');
            $table->string('type');
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
        Schema::dropIfExists('wallet_wallet_cards');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallet_package_purchases');
        Schema::dropIfExists('wallet_wallets');
    }
}
