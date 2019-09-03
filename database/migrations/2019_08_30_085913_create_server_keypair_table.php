<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerKeypairTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_keypair', function (Blueprint $table) {
            $table->bigInteger('keypair_id')->unsigned()->index();
            $table->foreign('keypair_id')->references('id')->on('key_pairs')->onDelete('cascade');
            $table->bigInteger('server_id')->unsigned()->index();
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->primary(['keypair_id', 'server_id']);
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
        Schema::dropIfExists('server_keypair');
    }
}
