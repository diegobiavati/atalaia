# database/migrations/2024_01_01_000001_create_atalaia_base_schema.php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtalaiaBaseSchema extends Migration
{
    public function up()
    {
        // Tabelas de Apoio (Domínios)
        Schema::create('ano_formacao', function (Blueprint $table) {
            $table->increments('id');
            $table->year('formacao')->unique();
            $table->date('data_matricula');
            $table->year('ano_per_basico')->nullable();
            $table->year('ano_per_qualificacao')->nullable();
            $table->string('ano_cfs', 7)->nullable();
            $table->enum('per_ativo_qualificacao', ['S', 'N'])->default('N');
        });

        Schema::create('omcts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sigla_omct', 50);
            $table->string('omct', 100);
            $table->string('gu', 50);
            $table->integer('status')->default(1);
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area', 100);
        });

        Schema::create('qms_matriz', function (Blueprint $table) {
            $table->increments('id');
            $table->string('qms', 100);
            $table->string('qms_sigla', 30);
            $table->string('qms_alias', 30)->default('');
            $table->enum('segmento', ['M', 'F'])->default('M');
            $table->string('img', 100);
            $table->string('img_ssaa', 50)->nullable();
            $table->float('vagas');
            $table->string('gu', 50)->nullable();
        });

        // Outras tabelas auxiliares do dump
        Schema::create('bancos', function (Blueprint $table) { $table->increments('id'); $table->string('nome_banco', 100)->nullable(); $table->integer('codigo_banco_central')->nullable(); $table->timestamps(); });
        Schema::create('ufs', function (Blueprint $table) { $table->increments('id'); $table->string('uf_sigla', 2)->nullable(); $table->string('uf_extenso', 50)->nullable(); $table->string('uf_regiao', 50)->nullable(); $table->timestamps(); });
        Schema::create('racas', function (Blueprint $table) { $table->increments('id'); $table->string('descricao', 70)->nullable(); $table->timestamps(); });
        Schema::create('religiaos', function (Blueprint $table) { $table->increments('id'); $table->string('religiao', 70)->nullable(); $table->string('categoria', 70)->nullable(); $table->timestamps(); });
        Schema::create('escolaridades', function (Blueprint $table) { $table->increments('id'); $table->string('escolaridade', 70)->nullable(); $table->timestamps(); });
        Schema::create('estado_civils', function (Blueprint $table) { $table->increments('id'); $table->string('estado_civil', 100)->nullable(); $table->timestamps(); });
        Schema::create('profissaos', function (Blueprint $table) { $table->increments('id'); $table->string('profissao', 100)->nullable(); $table->string('militar_postograd', 100)->nullable(); $table->timestamps(); });
        Schema::create('postograd', function (Blueprint $table) { $table->integer('id')->primary(); $table->string('postograd_abrev', 50)->nullable(); $table->string('postograd', 60)->nullable(); });
    }

    public function down() { /* Drop tables in reverse order */ }
}