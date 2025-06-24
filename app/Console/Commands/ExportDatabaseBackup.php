<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExportDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:multi-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta múltiplos bancos de dados (tabelas + views) para um único arquivo .sql';

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
        $connections = [
            'mysql' => env('DB_DATABASE'),        // conexão principal
            'mysql_ssaa'  => env('DB_DATABASE_SSAA'),   // conexão adicional
        ];

        $sqlDump = "-- Backup de múltiplos bancos\n";
        $sqlDump .= "-- Gerado em: " . now() . "\n\n";

        foreach ($connections as $connName => $dbName) {
            $this->info("🎯 Exportando banco: $dbName (conexão: $connName)");

            $tableKey = 'Tables_in_' . $dbName;

            // Executa SHOW TABLES/VIEW após trocar de banco
            $tables = DB::connection($connName)->select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $views  = DB::connection($connName)->select('SHOW FULL TABLES WHERE Table_type = "VIEW"');

            $sqlDump .= "--\n-- Banco de dados: `$dbName`\n--\n";
            $sqlDump .= "CREATE DATABASE IF NOT EXISTS `$dbName`;\nUSE `$dbName`;\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            // ▶️ Tabelas
            foreach ($tables as $tableObj) {
                $table = $tableObj->$tableKey;
                $this->info("📦 Tabela: $table");

                $createRow = (array) DB::connection($connName)->select("SHOW CREATE TABLE `$table`")[0];
                $createSql = $createRow['Create Table'] ?? null;

                if (!$createSql) {
                    $this->warn("⚠️ CREATE TABLE não encontrado para `$table`");
                    continue;
                }

                $sqlDump .= "-- Estrutura da tabela `$table`\n";
                $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlDump .= $createSql . ";\n\n";

                $columns = Schema::connection($connName)->getColumnListing($table);

                DB::connection($connName)->table($table)->orderBy($columns[0])->chunk(500, function ($rows) use (&$sqlDump, $columns, $table) {
                    foreach ($rows as $row) {
                        $values = array_map(function ($col) use ($row) {
                            $v = $row->$col;
                            return is_null($v) ? 'NULL' : "'" . addslashes($v) . "'";
                        }, $columns);

                        $sqlDump .= "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $values) . ");\n";
                    }
                });

                $sqlDump .= "\n\n";
            }

            // ▶️ Views
            foreach ($views as $viewObj) {
                $view = $viewObj->$tableKey;
                $this->info("🪟 View: $view");

                $createViewRow = (array) DB::connection($connName)->select("SHOW CREATE VIEW `$view`")[0];
                $createView = $createViewRow['Create View'] ?? null;

                if (!$createView) {
                    $this->warn("⚠️ CREATE VIEW não encontrado para `$view`");
                    continue;
                }

                $sqlDump .= "-- View `$view`\n";
                $sqlDump .= "DROP VIEW IF EXISTS `$view`;\n";
                $sqlDump .= $createView . ";\n\n";
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
        }

        $filename = 'backups/multibackup_' . now()->format('dmY_His') . '.sql';
        Storage::put($filename, $sqlDump);
        $this->info("✅ Backup .sql salvo em: storage/app/public/$filename");

        // Comprimir em .gz
        $originalPath = storage_path("app/public/$filename");
        $gzPath = $originalPath . '.gz';

        $gz = gzopen($gzPath, 'w9');
        gzwrite($gz, file_get_contents($originalPath));
        gzclose($gz);

        // (Opcional) remover o .sql original
        unlink($originalPath);

        $this->info("📦 Backup comprimido: $gzPath");
    }
}
