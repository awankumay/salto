<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionAuctionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_auction_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id');
            $table->integer('auction_id');
            $table->integer('user_id');
            $table->string('user_created');
            $table->timestamp('auction_date_created');
            $table->string('title');
            $table->integer('start_price');
            $table->integer('price_higher');
            $table->integer('buy_now')->nullable();
            $table->string('product_name');
            $table->string('product_categories_id');
            $table->string('product_categories_name');
            $table->integer('auction_log_id');
            $table->string('customer_id');
            $table->string('customer_name');
            $table->integer('customer_phone');
            $table->integer('customer_whatsapp')->nullable();
            $table->string('customer_email');
            $table->string('customer_zip_code');
            $table->string('customer_address');
            $table->string('customer_shipping_address');
            $table->timestamp('date_created');
            $table->timestamp('latest_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_auction_detail');
    }
}
