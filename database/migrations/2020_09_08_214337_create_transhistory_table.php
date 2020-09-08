<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranshistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transhistory', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_trans');
            $table->integer('users_id');
            $table->string('invoice');
            $table->integer('id_product');
            $table->integer('qty');
            $table->bigInteger('price');
            $table->date('date_payment');
            $table->integer('status');
            $table->longText('note');
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
        Schema::dropIfExists('transhistory');
    }
}
