# database/seeds/DatabaseSeeder.php
<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Postos e Graduações
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
        foreach ($postos as $p) DB::table('postograd')->insertIgnore($p);

        // 2. Unidades (OMCT)
        DB::table('omcts')->insertIgnore([
            'id' => 1, 'sigla_omct' => 'ESA', 'omct' => 'Escola de Sargentos das Armas', 'gu' => 'Três Corações-MG', 'status' => 1
        ]);

        // 3. Matriz de Especialidades (QMS)
        DB::table('qms_matriz')->insertIgnore([
            'id' => 9999, 'qms' => 'ADMINISTRAÇÃO ESA', 'qms_sigla' => 'ADM', 'qms_alias' => 'admin', 'segmento' => 'M', 'img' => '/images/logo_esa.png', 'vagas' => 0, 'gu' => 'Três Corações'
        ]);

        // 4. Tipos de Operadores (Funções Atalaia e Gavião)
        $tipos = [
            ['id' => 1, 'funcao' => 'SUPER ADMINISTRADOR', 'funcao_abrev' => 'S-ADM', 'alias_funcao' => 'super-admin'],
            ['id' => 2, 'funcao' => 'COMANDANTE DE UETE', 'funcao_abrev' => 'Cmt UETE', 'alias_funcao' => 'cmt-uete'],
            ['id' => 4, 'funcao' => 'SARGENTEANTE', 'funcao_abrev' => 'Sgte', 'alias_funcao' => 'sgte'],
            ['id' => 9001, 'funcao' => 'COMANDANTE DE CIA', 'funcao_abrev' => 'Cmt Cia', 'alias_funcao' => 'cmt-cia'],
            ['id' => 9005, 'funcao' => 'OPERADOR SSAA', 'funcao_abrev' => 'Op SSAA', 'alias_funcao' => 'op-ssaa'],
            ['id' => 9999, 'funcao' => 'ADMINISTRADOR GAVIÃO', 'funcao_abrev' => 'ADM-G', 'alias_funcao' => 'adm-gaviao'],
        ];
        foreach ($tipos as $t) DB::table('operadores_tipo')->insertIgnore($t);

        // 5. Permissões (Vincular Administrador ao acesso total)
        DB::table('operadores_permissoes')->insertIgnore([
            'operadores_tipo_id' => 1,
            'permissoes' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41'
        ]);

        // 6. Usuário Admin
        $email = 'admin@admin.com';
        if (!DB::table('users')->where('email', $email)->exists()) {
            $userId = DB::table('users')->insertGetId([
                'email' => $email,
                'password' => bcrypt('admin123'),
                'imagens_id' => 1,
                'created_at' => now()
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
        }
    }
}