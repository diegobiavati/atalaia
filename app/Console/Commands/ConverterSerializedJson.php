<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConverterSerializedJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'converter:serialized 
                            {table : Nome da tabela}
                            {from : Nome da coluna serializada}
                            {to : Nome da coluna JSON}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte dados PHP serialized para JSON em uma tabela.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->argument('table');
        $from = $this->argument('from');
        $to = $this->argument('to');

        $this->info("Convertendo coluna [$from] em [$to] na tabela [$table]...");

        DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table, $from, $to) {
            foreach ($rows as $row) {
                $data = @unserialize($row->$from);

                // Se não for serializado, ignora
                if ($data === false && $row->$from !== 'b:0;') {
                    continue;
                }

                // Converte e grava
                DB::table($table)
                    ->where('id', $row->id)
                    ->update([$to => json_encode($data)]);
            }
        });

        $this->info('Conversão concluída com sucesso ✅');
    }
}
