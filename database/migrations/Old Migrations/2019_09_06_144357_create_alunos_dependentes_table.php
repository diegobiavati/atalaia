<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlunosDependentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alunos_dependentes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aluno')->comment('Coluna ID da Tabela Alunos');
            $table->unsignedInteger('id_parentesco')->nullable()->comment('Código de parentesco do dependente com o Aluno');
            $table->string('dep_nome_completo', 255)->nullable()->comment('Nome completo do dependente');
            $table->date('dep_data_nascimento')->nullable()->comment('Data de nascimento do dependente');
            $table->string('dep_naturalidade', 100)->nullable()->comment('Naturalidade do dependente');
            $table->string('dep_endereco', 255)->nullable()->comment('Endereço completo do dependente');
            $table->unsignedInteger('dep_id_profissao')->nullable()->comment('Código da profissão do dependente');
            $table->unsignedInteger('dep_id_escolaridade')->nullable()->comment('Código da escolaridade do dependente');
            $table->char('dep_trabalho_ativo', 1)->default('N')->nullable()->comment('O dependente trabalha?');
            $table->string('dep_trabalho_funcao', 255)->nullable()->comment('Função desempenhada pelo dependente, se este trabalhar');
            $table->string('dep_bi_publicacao', 255)->nullable()->comment('Boletim de publicação informando a dependência');
            $table->timestamps();

            
            //$table->foreign('id_aluno')->references('id')->on('alunos')->onUpdate('cascade');//Juliao remove o aluno em situações diversas dai não pode ter referencia
            $table->foreign('id_parentesco')->references('id')->on('parentescos')->onUpdate('cascade');
            $table->foreign('dep_id_profissao')->references('id')->on('profissaos')->onUpdate('cascade');
            $table->foreign('dep_id_escolaridade')->references('id')->on('escolaridades')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alunos_dependentes');
    }
}
