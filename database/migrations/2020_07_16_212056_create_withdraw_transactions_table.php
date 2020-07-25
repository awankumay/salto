<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('withdraw_transactions_type')->comment('1:campaign, 2:auction');
            $table->bigInteger('user_id');
            $table->bigInteger('beneficiary_account');
            $table->string('beneficiary_account_issuer');
            $table->string('beneficiary_account_name');
            $table->bigInteger('auction_id')->nullable();
            $table->bigInteger('campaign_id')->nullable();
            $table->string('user_created');
            $table->bigInteger('unique_id');
            $table->bigInteger('reff_id')->nullable();
            $table->bigInteger('amount');
            $table->longText('note')->nullable();
            $table->bigInteger('sender_account');
            $table->string('sender_account_issuer');
            $table->string('sender_account_name');
            $table->timestamp('date_created');
            $table->timestamp('date_send')->nullable();
            $table->timestamp('date_confirm')->nullable();
            $table->timestamp('date_reversal')->nullable();
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
        Schema::dropIfExists('withdraw_transactions');
    }
}
