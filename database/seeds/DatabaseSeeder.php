# database/seeds/DatabaseSeeder.php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('operadores')->truncate();
        DB::table('users')->truncate();
        DB::table('omcts')->truncate();
        DB::table('qms_matriz')->truncate();
        DB::table('postograd')->truncate();
        DB::table('operadores_tipo')->truncate();
        DB::table('operadores_permissoes')->truncate();

        // 2. Postos e Graduações
        $postos = [
            ['id' => 1, 'postograd_abrev' => 'Cel', 'postograd' => 'Coronel'],
            ['id' => 2, 'postograd_abrev' => 'Ten Cel', 'postograd' => 'Tenente Coronel'],
            ['id' => 3, 'postograd_abrev' => 'Maj', 'postograd' => 'Major'],
            ['id' => 4, 'postograd_abrev' => 'Cap', 'postograd' => 'Capitão'],
            ['id' => 5, 'postograd_abrev' => '1º Ten', 'postograd' => 'Primeiro Tenente'],
            ['id' => 6, 'postograd_abrev' => '2º Ten', 'postograd' => 'Segundo Tenente'],
            ['id' => 8, 'postograd_abrev' => 'S Ten', 'postograd' => 'Subtenente'],
            ['id' => 9, 'postograd_abrev' => '1º Sgt', 'postograd' => 'Primeiro Sargento'],
            ['id' => 10, 'postograd_abrev' => '2º Sgt', 'postograd' => 'Segundo Sargento'],
            ['id' => 11, 'postograd_abrev' => '3º Sgt', 'postograd' => 'Terceiro Sargento'],
        ];
        DB::table('postograd')->insert($postos);

        // 3. Unidades (ESA)
        DB::table('omcts')->insert([
            ['id' => 1, 'sigla_omct' => 'ESA', 'omct' => 'Escola de Sargentos das Armas', 'gu' => 'Três Corações-MG', 'status' => 1],
            ['id' => 99, 'sigla_omct' => 'ESA-ADM', 'omct' => 'ESA Administração', 'gu' => 'Três Corações-MG', 'status' => 1]
        ]);

        // 4. Matriz QMS
        DB::table('qms_matriz')->insert([
            'id' => 9999, 'qms' => 'ADMINISTRAÇÃO ESA', 'qms_sigla' => 'ADM', 'qms_alias' => 'admin', 'segmento' => 'M', 'img' => '/images/logo_esa.png', 'vagas' => 0, 'gu' => 'Três Corações'
        ]);

        // 5. Funções
        DB::table('operadores_tipo')->insert([
            ['id' => 1, 'funcao' => 'SUPER ADMINISTRADOR', 'funcao_abrev' => 'S-ADM', 'alias_funcao' => 'super-admin'],
            ['id' => 9999, 'funcao' => 'ADMINISTRADOR GAVIÃO', 'funcao_abrev' => 'ADM-G', 'alias_funcao' => 'adm-gaviao'],
        ]);

        // 6. Permissões - ADICIONADO PARA AMBAS AS FUNÇÕES
        $all_perms = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41';
        
        DB::table('operadores_permissoes')->insert([
            ['operadores_tipo_id' => 1, 'permissoes' => $all_perms],
            ['operadores_tipo_id' => 9999, 'permissoes' => $all_perms] // <--- Importante!
        ]);

        // 7. Usuário Admin
        $email = 'admin@admin.com';
        DB::table('users')->insert([
            'email' => $email,
            'password' => bcrypt('admin123'),
            'imagens_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('operadores')->insert([
            'nome' => 'ADMINISTRADOR DO SISTEMA',
            'nome_guerra' => 'ADMIN',
            'email' => $email,
            'postograd_id' => 1,
            'omcts_id' => 1,
            'qms_matriz_id' => 9999,
            'id_funcao_operador' => '1,9999',
            'tel_pronto_atendimento' => '3599999999',
            'ativo' => 'S'
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Seed finalizado com sucesso!');
    }
}