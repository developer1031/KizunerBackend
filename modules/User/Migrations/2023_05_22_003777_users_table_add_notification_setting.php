<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersTableAddNotificationSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->integer('hangout_help_notification')->default(1)->nullable();
            $table->integer('hangout_help_email_notification')->default(1)->nullable();
            $table->integer('message_notification')->default(1)->nullable();
            $table->integer('message_email_notification')->default(1)->nullable();
            $table->integer('follow_notification')->default(1)->nullable();
            $table->integer('follow_email_notification')->default(1)->nullable();
            $table->integer('comment_notification')->default(1)->nullable();
            $table->integer('comment_email_notification')->default(1)->nullable();
            $table->integer('like_notification')->default(1)->nullable();
            $table->integer('like_email_notification')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('hangout_help_notification');
            $table->dropColumn('hangout_help_email_notification');
            $table->dropColumn('message_notification');
            $table->dropColumn('message_email_notification');
            $table->dropColumn('follow_notification');
            $table->dropColumn('follow_email_notification');
            $table->dropColumn('comment_notification');
            $table->dropColumn('comment_email_notification');
            $table->dropColumn('like_notification');
            $table->dropColumn('like_email_notification');
        });
    }
}
