<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusToHangoutOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_offers', function (Blueprint $table) {
            $table->string('now_payments_id')->nullable();
            $table->string('payment_status')->nullable();
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
            $table->dropColumn('now_payments_id');
            $table->dropColumn('payment_status');
        });
    }
}
