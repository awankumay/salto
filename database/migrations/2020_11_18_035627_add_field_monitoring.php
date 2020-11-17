<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldMonitoring extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $set = ['tb_hukdis', 'tb_suket'];
        foreach ($set as $key => $value) {
            if($value=='tb_hukdis'){
                Schema::table($value, function (Blueprint $table) {
                    $table->integer('user_approve_level_1')->nullable();
                    $table->integer('status_level_1')->nullable();
                    $table->timestamp('date_approve_level_1')->nullable();
                    $table->longText('reason_level_1')->nullable();
                    $table->string('photo')->nullable();
                    $table->integer('grade')->nullable();
                    $table->integer('id_taruna')->nullable();
                });   
            }
            if($value=='tb_suket'){
                Schema::table($value, function (Blueprint $table) {
                    $table->integer('user_approve_level_1')->nullable();
                    $table->integer('status_level_1')->nullable();
                    $table->timestamp('date_approve_level_1')->nullable();
                    $table->longText('reason_level_1')->nullable();
                    $table->integer('user_approve_level_2')->nullable();
                    $table->integer('status_level_2')->nullable();
                    $table->timestamp('date_approve_level_2')->nullable();
                    $table->longText('reason_level_2')->nullable();
                    $table->string('photo')->nullable();
                    $table->integer('grade')->nullable();
                });     
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $set = ['tb_hukdis', 'tb_suket'];

        foreach ($set as $key => $value) {
            if($value=='tb_hukdis'){
                Schema::table($value, function (Blueprint $table) {
                     $table->dropColumn(['user_approve_level_1', 'status_level_1', 
                                'date_approve_level_1', 'reason_level_1', 'photo', 'grade', 'id_taruna']);
                });   
            }
            if($value=='tb_suket'){
                Schema::table($value, function (Blueprint $table) {
                    $table->dropColumn(['user_approve_level_1', 'status_level_1', 'date_approve_level_1', 'reason_level_1', 
                                        'photo', 'grade', 'user_approve_level_2', 'status_level_2', 
                                        'date_approve_level_2', 'reason_level_2',]);
               });     
            }
        }
        
    }
}
