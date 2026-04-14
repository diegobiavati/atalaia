<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfissaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profissaos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('profissao', 100)->nullable()->comment('Descrição da profissão do pai');
            $table->string('militar_postograd', 100)->nullable()->comment('Posto ou graduação, se a profissão for militar');
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
        Schema::dropIfExists('profissaos');
    }
}
