# database/migrations/2024_01_01_000002_create_atalaia_core_schema.php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtalaiaCoreSchema extends Migration
{
    public function up()
    {
        // Imagens e Usuários
        Schema::create('imagens', function (Blueprint $table) { $table->increments('id'); $table->string('imagem', 200)->default(''); });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->integer('imagens_id')->nullable();
            $table->timestamps();
        });

        // Operadores
        Schema::create('operadores_tipo', function (Blueprint $table) { $table->increments('id'); $table->string('funcao', 100); $table->string('funcao_abrev', 20); $table->string('alias_funcao', 100); });
        
        Schema::create('operadores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 50);
            $table->string('nome_guerra', 100);
            $table->integer('postograd_id')->nullable();
            $table->integer('omcts_id')->nullable();
            $table->integer('qms_matriz_id')->nullable();
            $table->string('id_funcao_operador', 100)->default('');
            $table->string('tel_pronto_atendimento', 50);
            $table->string('email', 100);
            $table->enum('ativo', ['S', 'N'])->default('S');
        });

        // QMS da Escolha
        Schema::create('escolha_qms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identificador', 100);
            $table->integer('ano_formacao_id')->unsigned();
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');
            $table->longText('escolha_qms_masculino')->nullable();
            $table->longText('escolha_qms_feminino')->nullable();
            $table->timestamps();
            $table->foreign('ano_formacao_id')->references('id')->on('ano_formacao')->onDelete('cascade');
        });

        Schema::create('qms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qms_matriz_id');
            $table->string('qms', 100);
            $table->string('qms_sigla', 30);
            $table->integer('escolha_qms_id')->unsigned();
            $table->foreign('escolha_qms_id')->references('id')->on('escolha_qms')->onDelete('cascade');
        });

        // Tabela Alunos (Versão Simplificada do Dump)
        Schema::create('alunos', function (Blueprint $table) {
            $table->increments('id');
            $table->char('periodo_cfs', 2)->default('PB');
            $table->integer('numero');
            $table->string('nome_completo', 100);
            $table->string('nome_guerra', 100);
            $table->date('data_nascimento');
            $table->integer('data_matricula')->unsigned();
            $table->integer('omcts_id')->unsigned()->nullable();
            $table->integer('qms_id')->nullable();
            $table->integer('turma_esa_id')->unsigned()->nullable(); // Necessário para a View
            $table->enum('sexo', ['M', 'F'])->default('M');          // <--- A COLUNA QUE FALTOU
            $table->enum('atleta_marexaer', ['S', 'N'])->default('N'); // Necessário para a View
            $table->string('email', 100)->nullable();
            $table->string('doc_cpf', 30)->nullable();
            $table->string('doc_idt_civil', 40)->nullable();         // Necessário para a View
            $table->date('data_pracacfs')->nullable();               // Necessário para a View
            $table->integer('id_situacao_anterior')->nullable();     // Necessário para a View
            $table->integer('id_estado_civil')->nullable();         // Necessário para a View
            $table->integer('turma_id')->nullable();                 // Necessário para a View
            $table->string('al_inscricao', 20)->nullable();
            $table->timestamps();
            
            $table->foreign('data_matricula')->references('id')->on('ano_formacao');
            $table->foreign('omcts_id')->references('id')->on('omcts');
        });

        // Classificação e Notas
        Schema::create('alunos_classificacao', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aluno_id')->unsigned();
            $table->integer('classificacao')->nullable();
            $table->float('nota_final')->nullable();
            $table->float('nota_final_arredondada')->nullable();
            $table->json('data_demonstrativo_json')->nullable();
            $table->enum('reprovado', ['S', 'N'])->default('N');
            $table->integer('ano_formacao_id')->unsigned();
            $table->timestamps();
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
        });
        
        Schema::create('operadores_permissoes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operadores_tipo_id');
            $table->string('permissoes', 255)->default('');
        });

        Schema::create('permissoes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('permissao', 200);
        });

        Schema::create('situacoes_diversas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('situacao', 50);
            $table->string('descricao', 50)->nullable();
        });

        Schema::create('motivos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('motivo', 50);
        });

        Schema::create('turmas_pb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('turma', 50)->unique();
        });

        Schema::create('turmas_esa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('turma', 50)->nullable();
            $table->integer('qms_id')->nullable();
        });
    }

    public function down() { /* Reverse */ }
}