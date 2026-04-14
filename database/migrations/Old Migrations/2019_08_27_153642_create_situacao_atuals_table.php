<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSituacaoAtualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('situacao_atuals', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('situacao', 150)->nullable()->comment('Tipo de situação atual');
            $table->string('motivo', 150)->nullable()->comment('Motivo para a situação atual');
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
        Schema::dropIfExists('situacao_atuals');
    }
}
