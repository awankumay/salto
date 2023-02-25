<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuratHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_header', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_surat');
            $table->integer('id_category');
            $table->integer('status');
            $table->integer('user_approve_level_1')->nullable();
            $table->integer('user_approve_level_2')->nullable();
            $table->timestamp('date_approve_level_1')->nullable();
            $table->timestamp('date_approve_level_2')->nullable();
            $table->integer('reason_level_1')->nullable();
            $table->integer('reason_level_2')->nullable();
            $table->integer('user_created')->nullable();
            $table->integer('user_updated')->nullable();
            $table->integer('user_deleted')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('surat_header');
    }
}
