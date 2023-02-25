<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdentityToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity')->nullable();
            $table->integer('grade')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('regencie_id')->nullable();
            $table->string('stb')->nullable();
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
            $table->dropColumn(['identity', 'province_id', 'regencie_id', 'stb', 'grade']);
        });
    }
}
