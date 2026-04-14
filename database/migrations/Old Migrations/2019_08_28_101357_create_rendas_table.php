<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rendas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descricao_renda', 70)->nullable()->comment('Descrição da renda familiar');
            $table->char('ativo', 1)->default('S')->comment('Em uso?');
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
        Schema::dropIfExists('rendas');
    }
}
