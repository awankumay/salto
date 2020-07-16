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
            $table->integer('withdraw_transactions_type')->comment('1:campaign, 2:auction');
            $table->integer('user_id');
            $table->integer('beneficiary_account');
            $table->string('beneficiary_account_issuer');
            $table->string('beneficiary_account_name');
            $table->integer('auction_id')->nullable();
            $table->integer('campaign_id')->nullable();
            $table->string('user_created');
            $table->integer('unique_id');
            $table->integer('reff_id')->nullable();
            $table->integer('amount');
            $table->longText('note')->nullable();
            $table->integer('sender_account');
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
