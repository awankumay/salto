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
            $table->bigInteger('auction_id');
            $table->bigInteger('user_id');
            $table->string('user_name');
            $table->bigInteger('amount');
            $table->bigInteger('customer_id');
            $table->string('customer_name');
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
