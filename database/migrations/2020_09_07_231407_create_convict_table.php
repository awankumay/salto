<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convicts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->nullable();
            $table->bigInteger('identity')->nullable();
            $table->integer('identity_tipe')->nullable();
            $table->string('name');
            $table->string('type_convict')->nullable();
            $table->string('photo')->nullable();
            $table->string('document')->nullable();
            $table->string('violation')->nullable();
            $table->string('clause')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('address')->nullable();
            $table->string('block')->nullable();
            $table->string('lockup')->nullable();
            $table->integer('user_created')->nullable();
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
        Schema::dropIfExists('convicts');
    }
}
