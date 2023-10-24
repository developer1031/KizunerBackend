<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePaymentIdInHelpHelps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_helps', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('invoice_url');
            $table->dropColumn('stripe_intent_id');
            $table->dropColumn('now_payments_id');
            $table->dropColumn('stripe_refund_id');
            $table->dropColumn('now_payments_refund_id');
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
            $table->string('payment_status')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('stripe_intent_id')->nullable();
            $table->string('now_payments_id')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->string('now_payments_refund_id')->nullable();
        });
    }
}
