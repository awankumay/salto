<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('auction_id');
            $table->integer('user_id');
            $table->integer('user_name');
            $table->integer('amount');
            $table->integer('customer_id');
            $table->integer('customer_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auction_logs');
    }
}
