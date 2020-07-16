<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_type')->comment('1:campaign, 2:auction, 3:campaign barang');
            $table->integer('unique_id');
            $table->integer('reff_id')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('unique_amount')->default(0);
            $table->integer('total_amount')->default(0);
            $table->integer('sender_account')->nullable();
            $table->string('sender_account_issuer')->nullable();
            $table->string('sender_account_name')->nullable();
            $table->longText('note')->nullable();
            $table->timestamp('date_created');
            $table->timestamp('date_exp')->nullable();
            $table->timestamp('date_paid')->nullable();
            $table->timestamp('date_confirm')->nullable();
            $table->timestamp('date_reversal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
