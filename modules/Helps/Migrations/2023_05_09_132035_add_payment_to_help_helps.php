<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentToHelpHelps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_helps', function (Blueprint $table) {
            $table->string('payment_method', 20)->nullable();
            $table->double('amount')->nullable();
            $table->string('stripe_intent_id')->nullable();
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
            $table->dropColumn('payment_method');
            $table->dropColumn('amount');
            $table->dropColumn('stripe_intent_id');
        });
    }
}
