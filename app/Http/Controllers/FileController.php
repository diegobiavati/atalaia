<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{

    /**
     * Resolve o envio do arquivo.
     *
     * @param Request $request A instância do request.
     * @return Response A instância da response.
     */
    public function upload(Request $request, $name='file')
    {
        /*
         * O campo do form com o arquivo tinha o atributo name="file".
         */
        $file = $request->file($name);

        if (empty($file)) {
            abort(400, 'Nenhum arquivo foi enviado.');
        }

        /*
         * Já existe um arquivo igual ao que está sendo enviado?
         */
        if ($this->isAlreadyUploaded($file)) {
            abort(400, 'Esse mesmo arquivo já foi enviado antes.');
        }

        /*
         * Apenas grava o arquivo depois da verificação.
         */
        $filepath = 'uploads/'.date('Y').'/'.date('m');
        //$filepath = 'uploads';
        $path = $file->store($filepath);

        // Faça qualquer coisa com o arquivo enviado...
        return $filepath;
    }

    /**
     * Compara o conteúdo de dois arquivos para verificar se há diferenças.
     *
     * Não verifica qual exatamente é a diferença entre os arquivos. Dessa 
     * forma, uma diferenção é encontrada, a função para de ler os arquivos.
     *
     * @param SplFileInfo $a O primeiro arquivo para comparar.
     * @param SplFileInfo $b O segundo arquivo para comparar.
     * @return bool Indica se há qualquer diferença entre os arquivos.
     */
    private function fileDiff($a, $b)
    {
        $diff = false;
        $fa = $a->openFile();
        $fb = $b->openFile();

        /*
     * Lê o mesmo número de bytes de cada arquivo. Quebra (break) o loop 
     * assim que uma diferença for encontrada.
     */
        while (!$fa->eof() && !$fb->eof()) {
            if ($fa->fread(4096) !== $fb->fread(4096)) {
                $diff = true;
                break;
            }
        }

        /*
     * Apenas um dos arquivos chegou ao fim.
     */
        if ($fa->eof() !== $fb->eof()) {
            $diff = true;
        }

        /*
     * Closing handlers.
     */
        $fa = null;
        $fb = null;

        return $diff;
    }

    /**
     * Verifica se o arquivo passado já foi enviado antes.
     *
     * Passa de arquivo em arquivo no diretório de uploads, conferindo se algum 
     * pode ser igual ao arquivo sendo enviado.
     *
     * @param SplFileInfo $file O arquivo para verificar.
     * @return bool Indica se arquivo já foi enviando antes.
     */
    private function isAlreadyUploaded($file)
    {
        $size = $file->getSize();

        /*
     * O arquivo onde os arquivos são gravados.
     */
        $path = storage_path('app/uploads/');

        if (!is_dir($path)) {
            return false;
        }

        $files = scandir($path);
        foreach ($files as $f) {
            $filePath = $path . $f;
            if (!is_file($filePath)) {
                continue;
            }

            /*
         * Se ambos os arquivos tiverem o mesmo tamanho, compara o conteúdo.
         */
            if (filesize($filePath) === $size) {

                /*
             * Verifica se há alguma diferença, usando a função que escrevemos 
             * acima.
             */
                $diff = $this->fileDiff(new \SplFileInfo($filePath), $file);

                /*
             * Retorna se os arquivos **não** são diferentes, ou seja, iguais. 
             * Isso significa que já foi enviado antes.
             */
                return !$diff;
            }
        }
        return false;
    }
}
