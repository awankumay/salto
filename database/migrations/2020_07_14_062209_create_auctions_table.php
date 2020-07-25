<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('meta_title');
            $table->string('meta_description');
            $table->string('excerpt');
            $table->string('product_name');
            $table->string('product_categories_id');
            $table->string('product_categories_name');
            $table->longText('tags')->nullable();
            $table->longText('content');
            $table->string('photo')->nullable();
            $table->smallInteger('headline')->default(2);
            $table->smallInteger('status')->default(2);
            $table->bigInteger('user_id');
            $table->string('user_created');
            $table->string('user_updated')->nullable();
            $table->timestamp('date_published')->nullable();
            $table->timestamp('date_started')->nullable();
            $table->timestamp('date_ended')->nullable();
            $table->integer('buy_now')->nullable();
            $table->bigInteger('price_buy_now')->nullable();
            $table->bigInteger('start_price')->nullable();
            $table->integer('rate_donation')->nullable();
            $table->bigInteger('beneficiary_account');
            $table->string('beneficiary_account_issuer');
            $table->string('beneficiary_account_name');
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
        Schema::dropIfExists('auctions');
    }
}
