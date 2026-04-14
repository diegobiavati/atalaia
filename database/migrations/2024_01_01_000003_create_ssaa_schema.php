# database/migrations/2024_01_01_000003_create_ssaa_schema.php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSsaaSchema extends Migration
{
    protected $connection = 'mysql_ssaa';

    public function up()
    {
        Schema::connection($this->connection)->create('esa_disciplinas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_qms');
            $table->string('nome_disciplina', 50);
            $table->string('nome_disciplina_abrev', 50);
            $table->integer('carga_horaria')->default(0);
            $table->enum('tipo_disciplina', ['C', 'E', 'A'])->default('C');
            $table->enum('tfm', ['S', 'N'])->default('N');
            $table->timestamps();
        });

        Schema::connection($this->connection)->create('esa_avaliacoes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_esa_disciplinas')->unsigned();
            $table->enum('nome_avaliacao', ['AA','AA1','AA2','AA3','AF','AF1','AF2','AC','AC1','AC2','AR','AI','AD']);
            $table->integer('chamada');
            $table->decimal('peso', 4, 2)->default(0);
            $table->date('realizacao');
            $table->integer('gbm')->nullable();
            $table->timestamps();
            $table->foreign('id_esa_disciplinas')->references('id')->on('esa_disciplinas');
        });

        Schema::connection($this->connection)->create('esa_avaliacoes_indice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_esa_avaliacoes')->unsigned();
            $table->string('nr_item', 4);
            $table->integer('score_total')->unsigned()->default(0);
            $table->enum('tipo_questao', ['BD', 'N'])->default('N');
            $table->timestamps();
            $table->foreign('id_esa_avaliacoes')->references('id')->on('esa_avaliacoes');
        });

        Schema::connection($this->connection)->create('esa_avaliacoes_gbo', function (Blueprint $table) {
            $table->integer('id_esa_avaliacoes_indice')->unsigned();
            $table->integer('id_aluno'); // Sem FK pois Aluno está no outro banco
            $table->integer('score_vermelho')->nullable();
            $table->integer('id_operador');
            $table->timestamps();
            $table->primary(['id_esa_avaliacoes_indice', 'id_aluno']);
        });
    }

    public function down() { /* Reverse */ }
}