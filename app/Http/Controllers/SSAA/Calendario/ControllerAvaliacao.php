<?php

namespace App\Http\Controllers\SSAA\Calendario;

use App\Http\Controllers\Aluno\AlunoApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\EsaAvaliacoes;
use App\Models\EsaAvaliacoesDetalhes;
use App\Models\EsaAvaliacoesRap;
use App\Models\EsaDisciplinas;
use App\Models\QMS;
use App\Models\TurmasEsa;
use App\Rules\ESANomeAvaliacoes;
use App\Rules\TipoAvaliacoes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPUnit\Util\Json;

class ControllerAvaliacao extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    private function getAnoFormacao()
    {
        if (!session()->has('_anoFormacao')) {
            return view('ajax.erros.view-erro-padrao-centralizado')->with('mensagem', 'Refaça o login.');
        }

        $decrypt = decrypt(session('_anoFormacao'));

        return AnoFormacao::find(explode('-', $decrypt)[1]);
    }

    public function getComponentAjax()
    {

        $cursoSelecionado = QMS::find($this->_request->id_curso);
        $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

        $esaAvaliacoes = new EsaAvaliacoes;

        $readOnly = ($this->_ownauthcontroller->PermissaoCheck([33])) ? null : 'readOnly';

        return view('ssaa.avaliacao.componenteAjax', compact('disciplinas', 'cursoSelecionado', 'readOnly'))
            ->with('tipoAvaliacao', $esaAvaliacoes->getTodosTiposAvaliacoes())
            ->with('chamadas', $esaAvaliacoes->getTodasChamadas())
            ->with('nomeAvaliacoes', $esaAvaliacoes->getTodasAvaliacoes())
            ->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function viewListagemTurma()
    {
        return AlunoApiController::viewListagemTurma($this->_request);
    }

    public function index()
    {

        $anoFormacao = $this->getAnoFormacao();

        if (!$anoFormacao instanceof AnoFormacao) {
            return $anoFormacao;
        }

        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

        return view("ssaa.avaliacao.form", compact('cursos', 'anoFormacao'))->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function store()
    {
        if ($this->_ownauthcontroller->PermissaoCheck([33])) {
            $retorno['status'] = 'err';
            $retorno['response'] = [];

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaAvaliacoes = new EsaAvaliacoes;
                $esaAvaliacoes->fill(
                    [
                        'id_esa_disciplinas' => $this->_request->disciplinaID, 'nome_avaliacao' => $this->_request->nome_avaliacao, 'tipo_avaliacao' => $this->_request->tipo_avaliacao, 'chamada' => $this->_request->chamada, 'peso' => $this->_request->peso, 'proposta' => $this->_request->proposta, 'realizacao' => $this->_request->realizacao, 'devolucao' => $this->_request->devolucao
                    ]
                );

                $esaAvaliacoes->save();

                $retorno['status'] = 'success';
                $retorno['response'] = 'Registrado com sucesso.';
            }
        }

        return response()->json($retorno);
    }

    public function show($id)
    {
        if ($this->_ownauthcontroller->PermissaoCheck([33, 35])) {

            $anoFormacao = $this->getAnoFormacao();

            if (!$anoFormacao instanceof AnoFormacao) {
                return $anoFormacao;
            }

            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

            $esaAvaliacoes = EsaAvaliacoes::find($id);
            $cursoSelecionado = $esaAvaliacoes->esadisciplinas->qms;
            
            $rapLancadas = $esaAvaliacoes->esaAvaliacoesRap;

            $turmasRapPendente = $cursoSelecionado->consultaTurmas()->whereNotIn('id', $esaAvaliacoes->esaAvaliacoesRap->pluck('id_turmas_esa')->toArray());

            $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

            $readOnly = ($this->_ownauthcontroller->PermissaoCheck([33])) ? null : 'readOnly';

            return view('ssaa.avaliacao.form', compact('cursos', 'cursoSelecionado', 'rapLancadas', 'turmasRapPendente', 'disciplinas', 'esaAvaliacoes', 'readOnly'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('tipoAvaliacao', $esaAvaliacoes->getTodosTiposAvaliacoes())
                ->with('chamadas', $esaAvaliacoes->getTodasChamadas())
                ->with('nomeAvaliacoes', $esaAvaliacoes->getTodasAvaliacoes());
        } else {
            return view('ajax.erros.view-erro-padrao-centralizado')->with('mensagem', 'Usuário sem Permissão');
        }
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([33])) {

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaAvaliacoes = EsaAvaliacoes::find($id);

                $esaAvaliacoes->id_esa_disciplinas = $this->_request->disciplinaID;
                $esaAvaliacoes->nome_avaliacao = $this->_request->nome_avaliacao;
                $esaAvaliacoes->tipo_avaliacao = $this->_request->tipo_avaliacao;
                $esaAvaliacoes->chamada = $this->_request->chamada;
                $esaAvaliacoes->peso = $this->_request->peso;
                $esaAvaliacoes->proposta = $this->_request->proposta;
                $esaAvaliacoes->realizacao = $this->_request->realizacao;
                $esaAvaliacoes->devolucao = $this->_request->devolucao;

                if ($esaAvaliacoes->save()) {
                    $retorno['status'] = 'success';
                    array_push($retorno['response'], 'Avaliação modificada com sucesso.');
                } else {
                    array_push($retorno['response'], 'Ocorreu um erro.');
                }
            }
        }

        return response()->json($retorno);
    }

    public function destroy($id)
    {

        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([33])) {

            $esaAvaliacoes = EsaAvaliacoes::find($id);

            if ($esaAvaliacoes->delete()) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Deletado com sucesso.';
                return response()->json($retorno);
            } else {
                array_push($retorno['response'], 'Ocorreu um erro ao tentar remover a avaliação.');
            }
        } else {
            array_push($retorno['response'], 'Você não tem permissão para esta ação.');
        }

        return response()->json($retorno);
    }

    public function viewRelatorioAplicacaoProva()
    {
        $esaAvaliacoes = EsaAvaliacoes::find($this->_request->id_avaliacao);
        $rotaViewListagemTurma = '/gaviao/ajax/gerenciar-avaliacao/turma/';
        $rotaSalvaRap = '/gaviao/ajax/gerenciar-avaliacao/rap';

        $cursoSelecionado = $esaAvaliacoes->esadisciplinas->qms;
        $cursos = collect([$cursoSelecionado]);

        //Verifica se existe turmas já lançadas...
        $turmasLancadas = $esaAvaliacoes->esaAvaliacoesRap->pluck('id_turmas_esa')->toArray();
        $turmas = $cursoSelecionado->consultaTurmas()->whereNotIn('id', $turmasLancadas);
        
        return view('ssaa.avaliacao.rap.index', compact('cursos', 'cursoSelecionado', 'turmas', 'rotaViewListagemTurma', 'rotaSalvaRap'))
            ->with('esaAvaliacoes', $esaAvaliacoes)
            ->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function salvaRap(){
        $retorno['status'] = 'err';
        $retorno['response'] = [];

        $esaAvaliacoes = EsaAvaliacoes::find(explode("_", decrypt($this->_request->id_avaliacao))[0]);

        if($esaAvaliacoes){
            
            $json_alunos = null;
            if($this->_request->checkboxAlunos){
                foreach($this->_request->checkboxAlunos as $aluno){
                    $json_alunos[] = ['id_aluno' => (int)$aluno, 'motivo' => $this->_request->{'id_aluno_'.$aluno} ];
                }
            }

            $data = [
                'id_operador_devolucao' => session('login.operadorID'),
                'id_esa_avaliacoes' => $esaAvaliacoes->id,
                'id_turmas_esa' => (int)$this->_request->turmaID,
                'alunos_faltas' => (isset($json_alunos) ? json_encode($json_alunos) : null),
                'duracao' => $this->_request->duracao,
                'hora_inicio' => $this->_request->hora_inicio,
                'hora_termino' => $this->_request->hora_termino,
                'local_aplicacao' => $this->_request->local,
                'erros_impressao' => $this->_request->erros_impressao,
                'erros_interpretacao' => $this->_request->erros_interpretacao,
                'cond_local_adequacao' => $this->_request->radioAdequacao,
                'cond_local_arrumacao' => $this->_request->radioArrumacao,
                'cond_local_silencio' => $this->_request->radioSilencio,
                'cond_local_iluminacao' => $this->_request->radioIluminacao,
                'fatores_influencia_aplicacao' => $this->_request->fatores_influencia_aplicacao,
                'efetivo_realizou' => $this->_request->efetivo_realizou,
                'efetivo_termino' => $this->_request->efetivo_termino,
                'primeiro_discente' => json_encode(array('id_aluno' => (int)$this->_request->primeiro_discente, 'tempo' => $this->_request->tempo_primeiro_discente)),
                'segundo_discente' => json_encode(array('id_aluno' => (int)$this->_request->segundo_discente, 'tempo' => $this->_request->tempo_segundo_discente)),
                'terceiro_discente' => json_encode(array('id_aluno' => (int)$this->_request->terceiro_discente, 'tempo' => $this->_request->tempo_terceiro_discente)),
                'maioria_efetivo' => $this->_request->tempo_maioria_efetivo,
                'todo_efetivo' => $this->_request->tempo_todo_efetivo,
            ];
            
            $validador = $this->validaRapRequest($data);

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                if($esaAvaliacoes->esaAvaliacoesRap()->create($data)){
                    if($esaAvaliacoes->save()){
                        $retorno['status'] = 'success';
                        array_push($retorno['response'], 'Registrado com sucesso.');
                    }else{
                        array_push($retorno['response'], 'Ocorreu um erro ao salvar o RAP.');
                    }
                }else{
                    array_push($retorno['response'], 'Ocorreu um erro ao salvar os detalhes do RAP.');
                }
            }
        }else{
            array_push($retorno['response'], 'Avaliação não existente.');
        }

        return response()->json($retorno);
    }

    private function validaRapRequest($array_data){

        $array_data = array_merge($array_data, array('alunos_faltas' => json_decode($array_data['alunos_faltas'], true)));

        $array_data = array_merge($array_data, array('primeiro_discente' => json_decode($array_data['primeiro_discente'], true)));
        $array_data = array_merge($array_data, array('segundo_discente' => json_decode($array_data['segundo_discente'], true)));
        $array_data = array_merge($array_data, array('terceiro_discente' => json_decode($array_data['terceiro_discente'], true)));

        return Validator::make(
            $array_data,
            [
                'id_operador_devolucao' => 'required|numeric|exists:mysql.operadores,id',
                'id_esa_avaliacoes' => 'required|numeric|exists:mysql_ssaa.esa_avaliacoes,id',
                'id_turmas_esa' => 'required|numeric|exists:mysql.turmas_esa,id',
                'alunos_faltas.*.id_aluno' => 'nullable|exists:mysql.alunos,id',
                'duracao' => 'required|date_format:H:i',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_termino' => 'required|date_format:H:i',
                'local_aplicacao' => 'required|string',
                'erros_impressao' => 'nullable|string',
                'erros_interpretacao' => 'nullable|string',
                'cond_local_adequacao' => ['required', Rule::in(['MB', 'B', 'R', 'I'])],
                'cond_local_arrumacao' => ['required', Rule::in(['MB', 'B', 'R', 'I'])],
                'cond_local_silencio' => ['required', Rule::in(['MB', 'B', 'R', 'I'])],
                'cond_local_iluminacao' => ['required', Rule::in(['MB', 'B', 'R', 'I'])],
                'fatores_influencia_aplicacao' => 'nullable|string',
                'efetivo_realizou' => 'required|integer',
                'efetivo_termino' => 'required|integer',
                'primeiro_discente.tempo' => 'required|date_format:H:i',
                'primeiro_discente.id_aluno' => 'required|numeric|exists:mysql.alunos,id',
                'segundo_discente.tempo' => 'required|date_format:H:i',
                'segundo_discente.id_aluno' => 'required|numeric|exists:mysql.alunos,id',
                'terceiro_discente.tempo' => 'required|date_format:H:i',
                'terceiro_discente.id_aluno' => 'required|numeric|exists:mysql.alunos,id',
                'maioria_efetivo' => 'required|date_format:H:i',
                'todo_efetivo' => 'required|date_format:H:i',
            ],
            [
                'id_operador_devolucao.exists' => '<b>Operador não encontrado ou a sessão finalizou.</b>',
                'id_esa_avaliacoes.required' => '<b>Avaliação não encontrada</b>.',
                'id_turmas_esa.required' => 'O campo <b>Turma</b> é obrigatório.',

                'alunos_faltas.*.id_aluno.exists' => 'O aluno informado na falta não existe.',

                'duracao.required' => 'O campo <b>Duração prevista</b> é obrigatório.',
                'hora_inicio.required' => 'O campo <b>Horário de início</b> é obrigatório.',
                'hora_termino.required' => 'O campo <b>Horário de término</b> é obrigatório.',
                'local_aplicacao.required' => 'O campo <b>Local de aplicação</b> é obrigatório.',
                'cond_local_adequacao.required' => 'O campo <b>Condições do local de aplicação (Adequação)</b> é obrigatório.',
                'cond_local_arrumacao.required' => 'O campo <b>Condições do local de aplicação (Arrumação)</b> é obrigatório.',
                'cond_local_silencio.required' => 'O campo <b>Condições do local de aplicação (Silêncio)</b> é obrigatório.',
                'cond_local_iluminacao.required' => 'O campo <b>Condições do local de aplicação (Iluminação)</b> é obrigatório.',
                'efetivo_realizou.required' => 'O campo <b>Efetivo que realizou a prova</b> é obrigatório.',
                'efetivo_termino.required' => 'O campo <b>Efetivo na sala ao término do tempo</b> é obrigatório.',

                'primeiro_discente.id_aluno.required' => 'O campo <b>Primeiro Discente</b> é obrigatório.',
                'primeiro_discente.id_aluno.numeric' => 'O campo <b>Primeiro Discente</b> deve ser um número.',
                'primeiro_discente.id_aluno.exists' => 'Informe o aluno no campo <b>Primeiro Discente</b>.',
                'primeiro_discente.tempo.required' => 'O campo <b>Tempo</b> do Primeiro Discente é obrigatório.',

                'segundo_discente.id_aluno.required' => 'O campo <b>Segundo Discente</b> é obrigatório.',
                'segundo_discente.id_aluno.numeric' => 'O campo <b>Segundo Discente</b> deve ser um número.',
                'segundo_discente.id_aluno.exists' => 'Informe o aluno no campo <b>Segundo Discente</b>.',
                'segundo_discente.tempo.required' => 'O campo <b>Tempo</b> do Segundo Discente é obrigatório.',

                'terceiro_discente.id_aluno.required' => 'O campo <b>Terceiro Discente</b> é obrigatório.',
                'terceiro_discente.id_aluno.numeric' => 'O campo <b>Terceiro Discente</b> deve ser um número.',
                'terceiro_discente.id_aluno.exists' => 'Informe o aluno no campo <b>Terceiro Discente</b>.',
                'terceiro_discente.tempo.required' => 'O campo <b>Tempo</b> do Terceiro Discente é obrigatório.',

                'maioria_efetivo.required' => 'O campo <b>Maioria (Meta da turma)</b> é obrigatório.',
                'todo_efetivo.required' => 'O campo <b>Todo o efetivo</b> é obrigatório.',
            ]
        );
    }

    public function validaRequest()
    {
        return Validator::make(
            $this->_request->all(),
            [
                'disciplinaID' => 'required|numeric|exists:mysql_ssaa.esa_disciplinas,id',
                'nome_avaliacao' => ['required', new ESANomeAvaliacoes],
                'tipo_avaliacao' => ['required', new TipoAvaliacoes],
                'chamada' => 'required|integer',
                'peso' => 'required|integer',
                'proposta' => 'required|date',
                'realizacao' => 'required|date',
                'devolucao' => 'required|date'
            ],
            [
                'disciplinaID.exists' => '<b>Disciplina não cadastrada.</b>',
                'disciplinaID.required' => 'O campo <b>disciplina</b> é obrigatório.',
                'nome_avaliacao.required' => 'O campo <b>avaliação</b> é obrigatória.',
                'tipo_avaliacao.required' => 'O campo <b>tipo de avaliação</b> é obrigatório.',
                'chamada.required' => 'O campo <b>chamada</b> é obrigatória.',
                'peso.required' => 'O campo <b>peso</b> é obrigatório e deve ser um número.',
                'proposta.required' => 'O campo <b>data de proposta</b> é obrigatória.',
                'realizacao.required' => 'O campo <b>data de realização</b> é obrigatória.',
                'devolucao.required' => 'O campo <b>data de devolução</b> é obrigatória.'
            ]
        );
    }
}
