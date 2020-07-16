<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('meta_title');
            $table->string('meta_description');
            $table->string('excerpt');
            $table->longText('tags')->nullable();
            $table->longText('content');
            $table->string('photo')->nullable();
            $table->smallInteger('headline')->default(2);
            $table->smallInteger('status')->default(2);
            $table->integer('user_id');
            $table->string('user_created');
            $table->string('user_updated')->nullable();
            $table->timestamp('date_published')->nullable();
            $table->timestamp('date_started')->nullable();
            $table->timestamp('date_ended')->nullable();
            $table->integer('fund_target')->nullable();
            $table->integer('beneficiary_account');
            $table->string('beneficiary_account_issuer');
            $table->string('beneficiary_account_name');
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
        Schema::dropIfExists('campaigns');
    }
}
