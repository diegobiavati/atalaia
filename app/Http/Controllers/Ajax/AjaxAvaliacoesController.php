<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesMostra;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Utilitarios\Chip_download;
use App\Models\AvaliacoesMostrasRespostas;
use App\Models\OMCT;

class AjaxAvaliacoesController extends Controller
{
    public function ViewListaArquivoMostra(Collection $avaliacoesMostra)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        return view('avaliacoes.avaliacoesListaArquivoMostra', compact('avaliacoesMostra'));
    }

    public function ViewListaArquivoRepostaMostra(Avaliacoes $avaliacao)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        return view('avaliacoes.avaliacoesListaArquivoRespostaMostra', compact('avaliacao'));
    }

    public function uploadArquivoMostra(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $data = $request->except(['_token']);
        $key = array_key_first($data);

        $explode = explode('_', $key);

        $fileController = new FileController();

        $filePath = $fileController->upload($request, $key);

        $retorno['success'] = 'ok';

        $avaliacoes = Avaliacoes::find($explode[2]);

        AvaliacoesMostra::create(['avaliacoes_id' => $avaliacoes->id, 'nome_arquivo' => $filePath . '/' . $request->file($key)->hashName(), 'operador_id' => session()->get('login')['operadorID'], 'omct_id' => session()->get('login')['omctID']]);

        $avaliacoes = Avaliacoes::find($explode[2]);

        $retorno['html'] = $this->ViewListaArquivoMostra($avaliacoes->avaliacoesMostra)->render();
        $retorno['id'] = $explode[2];

        return $retorno;
    }

    public function uploadRepostaArquivoMostra(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $data = $request->except(['_token']);
        $key = array_key_last($data);//pega o nome do arquivo

        $explode = explode('_', $key);

        $fileController = new FileController();

        $filePath = $fileController->upload($request, $key);

        $retorno['success'] = 'ok';

        $avaliacoes = Avaliacoes::find($explode[2]);

        if ($data['omctID'] == 'todas_omct') {
            $omcts = OMCT::where('id', '<>', 1)->get(); //Remove a ESA;
            foreach ($omcts as $omct) {
                $omctIds[] = $omct->id;
            }
        } else {
            $omctIds[] = $data['omctID'];
        }

        foreach ($omctIds as $omctId) {
            AvaliacoesMostrasRespostas::create(['avaliacoes_id' => $avaliacoes->id, 'nome_arquivo' => $filePath . '/' . $request->file($key)->hashName(), 'omct_id' => $omctId]);
        }

        $avaliacoes = Avaliacoes::find($explode[2]);

        $retorno['html'] = $this->ViewListaArquivoRepostaMostra($avaliacoes)->render();
        $retorno['id'] = $explode[2];

        return $retorno;
    }

    public function downloadArquivoMostra(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $avaliacoesMostra = AvaliacoesMostra::where([['id', '=', $request->id], ['nome_arquivo', 'like', '%' . $request->hash . '%']])->first();

        if (in_array(8, session()->get('login')['perfil'])) {
            $avaliacoesMostra->update(['status' => 'A']);//Atualiza para Status em Análise caso seja alguém com perfil de SSAA
        }

        $args = array('download_path'   =>  pathinfo($avaliacoesMostra->nome_arquivo, PATHINFO_DIRNAME) . '/',
                      'file'            =>  pathinfo($avaliacoesMostra->nome_arquivo, PATHINFO_BASENAME),
                      'extension_check' =>  true,
                      'referrer_check'  =>  false,
                      'referrer'        =>  null
        );

        $download = new chip_download($args);

        $download_hook = $download->get_download_hook();

        if ($download_hook['download'] == true) {
            $download->get_download();
        } else {
            return $download->chip_print($download_hook['message']);
        }
    }

    public function downloadArquivoRespostaMostra(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $avaliacoesMostraRespostas = AvaliacoesMostrasRespostas::where([['id', '=', $request->id], ['nome_arquivo', 'like', '%' . $request->hash . '%']])->first();

        if (!isset($avaliacoesMostraRespostas->operador_visu_id) && (session()->get('login')['omctID'] <> 1)) {
            $avaliacoesMostraRespostas->update(['visualizado' => 'S', 'operador_visu_id' => session()->get('login')['operadorID']]);//Atualiza para Status em Análise caso seja alguém com perfil de SSAA
        }

        $args = array('download_path'   =>  pathinfo($avaliacoesMostraRespostas->nome_arquivo, PATHINFO_DIRNAME) . '/',
                      'file'            =>  pathinfo($avaliacoesMostraRespostas->nome_arquivo, PATHINFO_BASENAME),
                      'extension_check' =>  true,
                      'referrer_check'  =>  false,
                      'referrer'        =>  null
        );

        $download = new chip_download($args);

        $download_hook = $download->get_download_hook();

        if ($download_hook['download'] == true) {
            $download->get_download();
        } else {
            return $download->chip_print($download_hook['message']);
        }
    }

    public function ViewRevisaoProva()
    {
        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $avaliacoesMostra = Avaliacoes::whereHas('avaliacoesMostra', function ($q) {
            $q->whereIn('status', array('P', 'A'));
        })->get();


        return view('avaliacoes.revisaoProva', compact('avaliacoesMostra'));
    }

    public function AprovaRevisaoUete(Request $request)
    {
        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        AvaliacoesMostra::find($request->id)->update(['status' => 'C']);

        $retorno['success'] = true;
        $retorno['retornoClass'] = 'ion-android-cloud-done';
        $retorno['retornoStyle'] = 'green';
        return response()->json($retorno);
    }
}
