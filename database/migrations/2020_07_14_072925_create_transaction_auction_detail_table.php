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
            $table->bigInteger('transaction_id');
            $table->bigInteger('auction_id');
            $table->bigInteger('user_id');
            $table->string('user_created');
            $table->timestamp('auction_date_created');
            $table->string('title');
            $table->bigInteger('start_price');
            $table->bigInteger('price_higher');
            $table->string('product_name');
            $table->string('product_categories_id');
            $table->string('product_categories_name');
            $table->bigInteger('auction_log_id');
            $table->string('customer_id');
            $table->string('customer_name');
            $table->bigInteger('customer_phone');
            $table->bigInteger('customer_whatsapp')->nullable();
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
