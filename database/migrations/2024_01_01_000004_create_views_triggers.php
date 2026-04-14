# database/migrations/2024_01_01_000004_create_views_triggers.php
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateViewsTriggers extends Migration
{
    public function up()
    {
        // 1. Criar View vw_alunos_esa
        DB::statement("DROP VIEW IF EXISTS vw_alunos_esa");
        DB::statement("
            CREATE OR REPLACE VIEW vw_alunos_esa AS 
            SELECT af.formacao, al.id as aluno_id, al.numero, al.sexo, al.nome_guerra, al.nome_completo
            FROM atalaia.alunos al
            JOIN atalaia.ano_formacao af ON af.id = al.data_matricula
            WHERE al.qms_id IS NOT NULL
        ");

        // 2. Trigger para atualizar o GBM automaticamente
        // Importante: No Laravel, triggers precisam ser via unprepared
        DB::unprepared("DROP TRIGGER IF EXISTS ssaa.esa_avaliacoes_indice_after_update");
        DB::unprepared("
            CREATE TRIGGER ssaa.esa_avaliacoes_indice_after_update 
            AFTER UPDATE ON ssaa.esa_avaliacoes_indice 
            FOR EACH ROW 
            BEGIN
                UPDATE ssaa.esa_avaliacoes 
                SET gbm = (SELECT SUM(score_total) FROM ssaa.esa_avaliacoes_indice WHERE id_esa_avaliacoes = NEW.id_esa_avaliacoes)
                WHERE id = NEW.id_esa_avaliacoes;
            END
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS vw_alunos_esa");
        DB::unprepared("DROP TRIGGER IF EXISTS ssaa.esa_avaliacoes_indice_after_update");
    }
}