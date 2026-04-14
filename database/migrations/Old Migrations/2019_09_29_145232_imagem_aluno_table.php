<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImagemAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagem_aluno', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aluno')->unique()->comment('Coluna ID da Tabela Alunos');
            $table->string('nome_arquivo', 255)->nullable()->comment('Nome do Arquivo');
            $table->timestamps();

            
            //$table->foreign('id_aluno')->references('id')->on('alunos')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imagem_aluno');
    }
}
