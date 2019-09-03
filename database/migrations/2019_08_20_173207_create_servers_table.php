<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('environment_id');
            $table->foreign('environment_id')->references('id')->on('environments');
            $table->unsignedBigInteger('cloud_id');
            $table->foreign('cloud_id')->references('id')->on('clouds')->onDelete('CASCADE');
            $table->string('status')->nullable();
            $table->string('image')->nullable();
            $table->string('private_address')->nullable();
            $table->string('public_address');
            $table->string('provider_id');
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
        Schema::dropIfExists('servers');
    }
}
