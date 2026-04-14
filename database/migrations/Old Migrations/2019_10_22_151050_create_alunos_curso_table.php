<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlunosCursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alunos_curso', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aluno')->comment('Coluna ID da Tabela Alunos');
            //$table->string('periodo_cadastro', 2)->default('PB')->comment('Período de cadastro do Aluno no banco de dados (cadastro realizado no Período Básico ou no Período de Qualificação)');
            $table->string('senha', 20)->comment('Senha do Aluno');
            $table->decimal('nota_cacfs', 10, 3)->comment('Nota do Aluno no Concurso de Admissão');
            //$table->integer('id_area')->comment('Código da Área');
            $table->integer('id_qmsnaipe')->default(99)->comment('Código de QMS ou Naipe (para Música)');
            //$table->integer('id_pb_omct')->default(0)->comment('Código da UETE no Período Básico do CFS');

            
            $table->decimal('pb_notafinal', 10, 3)->nullable()->comment('Nota final no Período Básico do CFS');
            $table->integer('pb_classificacao')->nullable()->comment('Classificação geral no Período Básico do CFS');
            $table->integer('pb_classificacao_area')->nullable()->comment('Classificação por área no Período Básico do CFS');
            $table->string('pb_mencao', 5)->nullable()->comment('Menção no Período Básico do CFS');

            $table->string('situacao_ca', 255)->nullable()->comment('Observações sobre a participação no Concurso de Admissão');

            $table->timestamps();

            //$table->foreign('id_aluno')->references('id')->on('alunos')->onUpdate('cascade');
            $table->foreign('id_qmsnaipe')->references('id')->on('qms_matriz')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alunos_curso');
    }
}
