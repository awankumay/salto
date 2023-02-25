<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RequestProfileUpdated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_profile_updated', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('email');
            $table->integer('sex');
            $table->string('photo');
            $table->integer('province_id');
            $table->integer('regencie_id');
            $table->integer('identity');
            $table->integer('grade');
            $table->integer('phone');
            $table->integer('whatsapp')->nullable();
            $table->longText('address')->nullable();
            $table->integer('status_approve');
            $table->integer('user_approve');
            $table->longText('reason')->nullable();
            $table->timestamp('date_approve')->nullable();
            $table->integer('user_created');
            $table->integer('user_updated')->nullable();
            $table->integer('user_deleted')->nullable();
            $table->softDeletes('deleted_at');
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
        Schema::dropIfExists('request_profile_updated');
    }
}
