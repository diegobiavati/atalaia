<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvertSerializedToJson extends Command
{
    protected $signature = 'bi:converter-json';
    protected $description = 'Converte dados serializados para JSON da Tabela Alunos Classificação! ';

    public function handle()
    {
        $registros = DB::table('alunos_classificacao')
            ->whereNull('data_demonstrativo_json')
            ->limit(500) //  importante para performance
            ->orderBy('id', 'DESC')
            ->get();

        if ($registros->isEmpty()) {
            $this->info('Nada para processar.');
            return;
        }

        foreach ($registros as $r) {
            try {

                $array = @unserialize($r->data_demonstrativo, ['allowed_classes' => false]);

                if (!$array) {
                    continue;
                }

                DB::table('alunos_classificacao')
                    ->where('id', $r->id)
                    ->update([
                        'data_demonstrativo_json' => json_encode($array, JSON_UNESCAPED_UNICODE),
                        'json_processado_at' => now()
                    ]);

            } catch (\Exception $e) {
                Log::channel('daily')->error("Erro ID {$r->id}: " . $e->getMessage());  
            }
        }

        $this->info('Processamento concluído.');
    }
}
