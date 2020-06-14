<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->longText('address')->nullable();
            $table->longText('description')->nullable();
            $table->longText('tagline')->nullable();
            $table->smallInteger('sex')->nullable();
            $table->smallInteger('status')->nullable();
            $table->string('photo')->nullable();
            $table->integer('user_type')->nullable();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'photo', 'address', 'sex', 'status', 'user_type', 'description', 'tagline']);
            $table->dropSoftDeletes();
        });
    }
}
