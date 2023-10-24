<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRangePriceToHangout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hangout_hangouts', function (Blueprint $table) {
            $table->integer('is_range_price')->nullable();
            $table->double('min_amount')->nullable();
            $table->double('max_amount')->nullable();
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
            $table->dropColumn('is_range_price');
            $table->dropColumn('min_amount');
            $table->dropColumn('max_amount');
        });
    }
}
