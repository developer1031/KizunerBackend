<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelRuleHelpOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_offers', function (Blueprint $table) {
            $table->string('subject_cancel',65535)->nullable();
            $table->string('message_cancel',65535)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_offers', function (Blueprint $table) {
            $table->dropColumn('subject_cancel');
            $table->dropColumn('message_cancel');
        });
    }
}
