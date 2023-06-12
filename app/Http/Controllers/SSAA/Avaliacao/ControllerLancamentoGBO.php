<?php

namespace App\Http\Controllers\SSAA\Avaliacao;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Models\EsaAvaliacoesIndices;
use App\Models\EsaAvaliacoesGbo;
use App\Models\AlunosClassificacao;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use App\Models\EsaAvaliacoes;

class ControllerLancamentoGBO extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;

    private $_urlComboAlunos = '/gaviao/ajax/view-combo-box-alunos/';
    private $_urlNavegacaoItem = '/gaviao/ajax/view-navegacao-item/';
    private $_urlLancamentoGBO = '/gaviao/ajax/view-lancamento-gbo';

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    public function index()
    {
        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {
            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];

            $esaAvaliacoes = EsaAvaliacoes::find($id_esa_avaliacoes);
            $turmas = $esaAvaliacoes->esadisciplinas->qms->consultaTurmas();
            $gbm = ControllerIndiceDificuldades::getGBM()->getData()->resultado_gbm;
            $gbo = 0;

            //Verifica se é Par
            if(($gbm % 2) != 0){
                return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'O GBM ['. $gbm. '] informado não pode ser ímpar');
            }
            $alunos = collect();

            $criptografia = true;

            return view('ssaa.avaliacao.indice.gbo.index', compact('esaAvaliacoes', 'turmas', 'alunos', 'criptografia', 'gbm', 'gbo'))
                        ->with('ownauthcontroller', $this->_ownauthcontroller)
                        ->with('urlComboAlunos', $this->_urlComboAlunos)
                        ->with('urlNavegacaoItem', $this->_urlNavegacaoItem);
        }else{
            return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'Usuário sem Permissão');
        }
    }

    public function show($id)
    {
        return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'Usuário sem Permissão');
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Usuário sem Permissão Ou a Sessão está expirada.';
        
        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {

            $id_indice = (int)explode('_', decrypt($id))[2]; 
            $id_aluno = (int)explode('_', decrypt($this->_request->id_aluno))[2];
            $score_vermelho = $this->_request->score_vermelho;
            
            $esaAvaliacoesGbo = EsaAvaliacoesGBO::where([['id_esa_avaliacoes_indice', '=', $id_indice], ['id_aluno', '=', $id_aluno]])
                            ->firstOrCreate(array('id_esa_avaliacoes_indice' => $id_indice, 'id_aluno' => $id_aluno, 'id_operador' => session('login.operadorID')) );

            if(isset($esaAvaliacoesGbo)){
                if($score_vermelho > $esaAvaliacoesGbo->esaAvaliacoesIndices->score_total){
                    $retorno['response'] = 'Score lançado é maior que '.$esaAvaliacoesGbo->esaAvaliacoesIndices->score_total.'.';
                }elseif($score_vermelho < 0){
                    $retorno['response'] = 'Score lançado é menor que 0.';
                }elseif(is_null($score_vermelho)){
                    $retorno['response'] = 'Informe um Score válido.';
                }else{
                    $esaAvaliacoesGbo->score_vermelho = $score_vermelho;
                    $esaAvaliacoesGbo->id_operador = session('login.operadorID');
                    $esaAvaliacoesGbo->save();    

                    $retorno['status'] = 'success';
                    $retorno['response'] = 'GBO Registrado.';
                }
            }else{
                $retorno['response'] = 'Entre em contato com o desenvolvedor.';
            }
        }
        return response()->json($retorno);    
    }

    public function destroy($id){
        $retorno['status'] = 'err';
        $retorno['response'] = 'Ocorreu um erro.';

        return response()->json($retorno);   
    }

    public function viewPaginacaoLancamento(){

        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {
            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];
            $id_aluno = explode('_', decrypt($this->_request->id_aluno))[1];
            $requisicao = $this->_request->requisicao ?? null;
            $item = (!is_null($this->_request->item)) ? explode('_', decrypt($this->_request->item))[2] : null;
            $selecionado = null;

            $esaAvaliacoesIndices = new EsaAvaliacoesIndices();
            
            $esaAvaliacoesIndices = $esaAvaliacoesIndices->getAlunoIndicesItens($id_esa_avaliacoes, $id_aluno)->get();

            for($i = 0;$i < $esaAvaliacoesIndices->count(); $i++){

                if(!is_null($item) && $esaAvaliacoesIndices->get($i)->id == $item){
                    switch($requisicao){
                        case 'anterior':
                            $selecionado = ($i - 1);
                            break;
                        case 'proximo':
                            $selecionado = ($i + 1);
                            break;
                    }
                }elseif(is_null($esaAvaliacoesIndices->get($i)->score_vermelho)){
                    $selecionado = $i;
                }

                if(!is_null($selecionado)){
                    break;
                }
            }

            if(is_null($selecionado)){
                $mensagem = 'Terminou!!!';
                return view('ssaa.avaliacao.indice.gbo.mensagem', compact('mensagem'))->with('gbo', $this->getGBO($id_esa_avaliacoes, $id_aluno)->getData()->resultado_gbo);
            }

            return view('ssaa.avaliacao.indice.gbo.componente-gbo', compact('esaAvaliacoesIndices', 'selecionado'))
                        ->with('id_indice', $esaAvaliacoesIndices->get($selecionado)->id)
                        ->with('id_aluno', $id_aluno)
                        ->with('urlLancamentoGBO', $this->_urlLancamentoGBO)
                        ->with('gbo', $this->getGBO($id_esa_avaliacoes, $id_aluno)->getData()->resultado_gbo);
        }
    }
    
    public static function getGBO($id_esa_avaliacoes, $id_aluno){
        $retorno['success'] = true;

        $gbm = ControllerIndiceDificuldades::getGBM()->getData()->resultado_gbm;

        $esaAvaliacoesIndices = new EsaAvaliacoesIndices();
        $retorno['resultado_gbo'] = ($gbm - $esaAvaliacoesIndices->getAlunoIndicesItens($id_esa_avaliacoes, $id_aluno)->sum('score_vermelho'));
        
        return response()->json($retorno);
    }
}