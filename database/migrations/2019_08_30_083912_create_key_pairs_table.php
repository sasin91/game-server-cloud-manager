<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_pairs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('owner');
            $table->text('encryption_key');
            $table->text('public_key');
            $table->text('private_key');
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
        Schema::drop('key_pairs');
    }
}
