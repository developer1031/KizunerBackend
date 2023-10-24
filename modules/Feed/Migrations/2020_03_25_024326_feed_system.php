<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FeedSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** User will follow a feed channel if they are friends, or follower but not block */
        Schema::create('feed_followers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('channel_id')->nullable();
            $table->string('status')->default('active');//'active', 'inactive'
            $table->string('scope')->default('default');//'default', 'notification'
            $table->softDeletesTz();
            $table->timestampsTz();
            $table->index('user_id');
            $table->index('channel_id');
            $table->index('status');
        });

        Schema::create('feed_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->uuid('reference_user_id')->nullable();
            $table->string('type')->nullable();//hangout, status
            $table->string('status')->default('new');//'new', 'active', 'inactive'
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_timelines');
        Schema::dropIfExists('feed_messages');
        Schema::dropIfExists('feed_followers');
    }
}
