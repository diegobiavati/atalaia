<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapaOutrosDadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapa_outros_dados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('omct_id')->comment('Código da UETE');
            $table->integer('area_id')->comment('Código da área');
            $table->integer('ano_formacao_id')->comment('Código Ano Formação');
            $table->enum('sexo', ['M', 'F'])->comment('Sexo');
            $table->unsignedInteger('qtdade_previstomtcl')->default(0)->comment('Quantidade de Alunos previstos para matrícula');
            $table->unsignedInteger('qtdade_designadomtcl')->default(0)->comment('Quantidade de Alunos designados para matrícula');
            $table->unsignedInteger('qtdade_adiamentomtcl')->default(0)->comment('Quantidade de Alunos que adiaram matrícula');
            $table->unsignedInteger('qtdade_em_1mtcl')->default(0)->comment('Quantidade de Alunos em primeira matrícula');
            $table->unsignedInteger('qtdade_em_2mtcl')->default(0)->comment('Quantidade de Alunos em segunda matrícula (rematriculados)');
            $table->unsignedInteger('qtdade_em_mtcladiamento')->default(0)->comment('Quantidade de Alunos que matricularam por adiamento em ano anterior');
            $table->unsignedInteger('qtdade_em_mtclordjudicial')->default(0)->comment('Quantidade de Alunos matriculados por ordem judicial');
            $table->unsignedInteger('qtdade_pqessa')->default(0)->comment('Quantidade de Alunos matriculados na ESA');
            $table->unsignedInteger('qtdade_pqesslog')->default(0)->comment('Quantidade de Alunos matriculados na EsLog');
            $table->unsignedInteger('qtdade_pqciavex')->default(0)->comment('Quantidade de Alunos matriculados no CIAVEx');

            $table->timestamps();

            $table->foreign('omct_id')->references('id')->on('omcts')->onUpdate('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onUpdate('cascade');
            $table->foreign('ano_formacao_id')->references('id')->on('ano_formacao')->onUpdate('cascade');

            $table->unique(['omct_id', 'area_id', 'ano_formacao_id', 'sexo'], 'mapa_outros_dados_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapa_outros_dados');
    }
}
