<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodAndAmountToHangout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_hangouts', function (Blueprint $table) {
            $table->string('payment_method', 20)->nullable();
            $table->double('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hangout_hangouts', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('amount');
        });
    }
}
