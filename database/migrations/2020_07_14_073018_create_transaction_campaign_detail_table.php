<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateTransactionCampaignDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_campaign_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id');
            $table->integer('campaign_id');
            $table->string('title');
            $table->string('user_created');
            $table->integer('user_id');
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('customer_phone')->nullable();
            $table->integer('customer_whatsapp')->nullable();
            $table->string('customer_email')->nullable();
            $table->timestamp('date_created');
            $table->timestamp('latest_updated');
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
        Schema::dropIfExists('transaction_campaign_detail');
    }
}
