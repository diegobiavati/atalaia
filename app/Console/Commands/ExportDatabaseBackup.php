<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExportDatabaseBackup extends Command
{
    /**
     * Nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'backup:multi-sql';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Exporta múltiplos bancos de dados (tabelas + views) para um único arquivo .sql compatível entre MySQL 5.6–8.0';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $connections = [
            'mysql'       => env('DB_DATABASE'),         // conexão principal
            'mysql_ssaa'  => env('DB_DATABASE_SSAA'),    // conexão adicional
        ];

        $sqlDump = "-- Backup de múltiplos bancos\n";
        $sqlDump .= "-- Gerado em: " . now() . "\n\n";

        foreach ($connections as $connName => $dbName) {
            if (!$dbName) {
                $this->warn("⚠️ Conexão '$connName' ignorada — banco não definido no .env");
                continue;
            }

            $this->info("🎯 Exportando banco: $dbName (conexão: $connName)");

            $tableKey = 'Tables_in_' . $dbName;

            // Listar tabelas e views
            $tables = DB::connection($connName)->select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $views  = DB::connection($connName)->select('SHOW FULL TABLES WHERE Table_type = "VIEW"');

            $sqlDump .= "--\n-- Banco de dados: `$dbName`\n--\n";
            $sqlDump .= "CREATE DATABASE IF NOT EXISTS `$dbName`;\nUSE `$dbName`;\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            // Desativar logs de queries para desempenho
            DB::connection($connName)->disableQueryLog();

            // ▶️ Exportar tabelas
            foreach ($tables as $tableObj) {
                $table = $tableObj->$tableKey;
                $this->info("📦 Tabela: $table");

                $createRow = (array) DB::connection($connName)->select("SHOW CREATE TABLE `$table`")[0];
                $createSql = $createRow['Create Table'] ?? null;

                if (!$createSql) {
                    $this->warn("⚠️ CREATE TABLE não encontrado para `$table`");
                    continue;
                }

                // Corrigir collations incompatíveis
                $createSql = preg_replace('/utf8mb4_0900_ai_ci/i', 'utf8mb4_unicode_ci', $createSql);
                $createSql = preg_replace('/utf8mb4_0900_as_ci/i', 'utf8mb4_unicode_ci', $createSql);

                $sqlDump .= "-- Estrutura da tabela `$table`\n";
                $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlDump .= $createSql . ";\n\n";

                // Obter colunas corretamente (SHOW COLUMNS é mais estável)
                $columns = collect(DB::connection($connName)->select("SHOW COLUMNS FROM `$table`"))
                    ->pluck('Field')
                    ->toArray();

                $hashSet = [];

                DB::connection($connName)
                    ->table($table)
                    ->orderBy($columns[0])
                    ->chunk(500, function ($rows) use (&$sqlDump, $columns, $table, &$hashSet) {
                        foreach ($rows as $row) {
                            $values = array_map(function ($col) use ($row) {
                                $v = $row->$col;
                                return is_null($v) ? 'NULL' : "'" . addslashes($v) . "'";
                            }, $columns);

                            $line = "INSERT IGNORE INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $values) . ");";
                            $hash = md5($line);

                            // Evita registros duplicados no dump
                            if (!isset($hashSet[$hash])) {
                                $hashSet[$hash] = true;
                                $sqlDump .= $line . "\n";
                            }
                        }
                    });

                $sqlDump .= "\n\n";
            }

            // ▶️ Exportar views
            foreach ($views as $viewObj) {
                $view = $viewObj->$tableKey;
                $this->info("🪟 View: $view");

                $createViewRow = (array) DB::connection($connName)->select("SHOW CREATE VIEW `$view`")[0];
                $createView = $createViewRow['Create View'] ?? null;

                if (!$createView) {
                    $this->warn("⚠️ CREATE VIEW não encontrado para `$view`");
                    continue;
                }

                // Corrigir collations
                $createView = preg_replace('/utf8mb4_0900_ai_ci/i', 'utf8mb4_unicode_ci', $createView);
                $createView = preg_replace('/utf8mb4_0900_as_ci/i', 'utf8mb4_unicode_ci', $createView);

                $sqlDump .= "-- View `$view`\n";
                $sqlDump .= "DROP VIEW IF EXISTS `$view`;\n";
                $sqlDump .= $createView . ";\n\n";
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
        }

        // Caminho e nome do arquivo
        $filename = 'backups/' . date('Y') . '/' . date('m') . '/multibackup_' . now()->format('dmY_His') . '.sql';

        // Cria diretórios se necessário
        Storage::makeDirectory(dirname($filename));

        Storage::put($filename, $sqlDump);
        $this->info("✅ Backup .sql salvo em: storage/app/public/$filename");

        // Compactar em .gz
        $originalPath = storage_path("app/public/$filename");
        $gzPath = $originalPath . '.gz';

        $gz = gzopen($gzPath, 'w9');
        gzwrite($gz, file_get_contents($originalPath));
        gzclose($gz);

        // Remove o SQL original
        unlink($originalPath);

        $this->info("📦 Backup comprimido: $gzPath (" . round(filesize($gzPath)/1024/1024, 2) . " MB)");
    }
}
