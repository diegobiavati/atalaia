<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReligiaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('religiaos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('religiao', 70)->nullable()->comment('Descrição de religião');
            $table->string('categoria', 70)->nullable()->comment('Tipo de religião');
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
        Schema::dropIfExists('religiaos');
    }
}
