<?php

namespace App\Http\Controllers\Ajax;

//use Request;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/* MODELS */

use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\Disciplinas;
use App\Models\Operadores;
use App\Models\TelegramAlunoAuth;
use App\Models\TelegramMsgEnviadas;
/* OUTROS CONTROLLER */

use App\Http\Controllers\TelegramSendPostController;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxChatAlunoController extends Controller
{
    public function FaleComAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        // SELECIONANDO OS SOMENTE OS ALUNOS QUE JÁ FIZERAM SEU REGISTRO NO APP

        $alunos_autenticados = TelegramAlunoAuth::whereNotNull('chat_id')->get();

        // FAZENDO UMA ARRAY COM SOMENTE OS ALUNOS AUTENTICADOS (REGISTRADOS) DO ANO DE FORMAÇÃO CORRENTE

        foreach ($alunos_autenticados as $aluno) {
            if ($aluno->aluno->data_matricula == $id_ano_corrente) {
                $alunosIDs[] = $aluno->aluno_id;
            }
        }

        $alunosIDs = ($alunosIDs) ?? array();

        if ($ownauthcontroller->PermissaoCheck(1)) {
            $alunos = Alunos::whereIn('id', $alunosIDs)->get();
        } else {
            $omct = session()->get('login.omctID');
            $alunos = Alunos::whereIn('id', $alunosIDs)->where('omcts_id', $omct)->get();
        }

        return view('ajax.fale-com-aluno')->with('alunos', $alunos);
    }

    public function CarregarMensagens(Request $request)
    {
        if (isset($request->alunosCheck)) {
            if (count($request->alunosCheck) == 1) {
                foreach ($request->alunosCheck as $alunoID) {
                    $aluno = Alunos::find($alunoID);
                }

                $data['conversa_com'] = '<b>Mensagem para:</b> <span style="font-size: 12px;">' . $aluno->nome_guerra . '</span>';

                // VERIFICANDO SE HÁ HISTÓRICO DE MENSAGENS PARA ESTE ALUNO

                $operadorID = session()->get('login.operadorID');

                $telegramMsgEnviadas = TelegramMsgEnviadas::where('origem_operador_id', $operadorID)->where('destino_aluno_id', $alunoID)->orderBy('data_hora', 'asc')->get();

                if (count($telegramMsgEnviadas) == 0) {
                    if ($aluno->sexo == 'M') {
                        $data['box_mensagens'] = '<div class="empty_box" style="margin: 64px auto; width: 60%; background-color: #CEE3F6; color: #000000; font-size: 14px; padding: 12px; border-radius: 4px;"><b>Nenhuma mensagem foi enviada para este aluno ainda...</b></div>';
                    } else {
                        $data['box_mensagens'] = '<div class="empty_box" style="margin: 64px auto; width: 60%; background-color: #CEE3F6; color: #000000; font-size: 14px; padding: 12px; border-radius: 4px;"><b>Nenhuma mensagem foi enviada para esta aluna ainda...</b></div>';
                    }
                } else {
                    foreach ($telegramMsgEnviadas as $telegramMsg) {
                        $msg[] = '  <div style="font-size: 14px; text-align: left; padding: 10px; margin: 8px 0; background-color: #CEE3F6;">
                                        <p><b>Data da mensagem: </b>' . $telegramMsg->dataMsg() . '</p>
                                        <p>
                                            <b>Mensagem:</b>
                                            <p>' . $telegramMsg->msg . '</p>
                                        </p>
                                    </div>';
                    }

                    $data['box_mensagens'] = implode('', $msg);

                    $data['box_mensagens'] = strip_tags($data['box_mensagens'], '<p><div><b><span><br>');
                }
            } elseif (count($request->alunosCheck) > 1) {
                $alunos = Alunos::whereIn('id', $request->alunosCheck)->get();
                foreach ($alunos as $aluno) {
                    $alunos_dst[] = $aluno->nome_guerra;
                }

                $data['box_mensagens'] = '<div style="margin: 80px auto; width: 60%; background-color: #F7F8E0; color: #696969; font-size: 14px; padding: 12px; border-radius: 4px;"><b>Esta mensagem será entregue aos alunos selecionados</b></div>';

                $data['conversa_com'] = '<b>Mensagem para:</b> <span style="font-size: 12px;">' . str_limit(implode(', ', $alunos_dst), 418) . '</span>';
            }
        }

        return $data;
    }

    public function EnviarMensagemAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        if (isset($request->alunosCheck)) {
            // SELECIONANDO TODOS OS ALUNOS QUE ESTAO EM $request->alunosCheck

            $alunos = Alunos::whereIn('id', $request->alunosCheck)->get();
            foreach ($alunos as $aluno) {
                $aluno_nome[$aluno->id] = $aluno->nome_guerra;
            }

            $operadorID = session()->get('login.operadorID');
            $operador = Operadores::find($operadorID);

            foreach ($request->alunosCheck as $alunoID) {
                $msg = new TelegramMsgEnviadas();
                $msg->origem_operador_id = session()->get('login.operadorID');
                $msg->destino_aluno_id = $alunoID;
                $msg->usr_msg_destino = $aluno_nome[$alunoID];
                $msg->usr_msg_origem = $operador->postograd->postograd_abrev . ' ' . $operador->nome_guerra;
                $msg->data_hora = date('Y-m-d H:i:s');
                $msg->msg = nl2br(trim($request->text_msg_fale_com_aluno));
                $msg->save();

                // ENVIO DESTA MENSAGEM PARA O TELEGRAM DO ALUNO

                $sendMsg = new TelegramSendPostController();

                $sendMsg->sendTelegramMessage($alunoID, $request->text_msg_fale_com_aluno);
            }

            $data['box_mensagens'] = '<div style="font-size: 14px; text-align: left; padding: 10px; margin: 8px 0; background-color: #CEE3F6;">
                                        <p><b>Data da mensagem: </b>' . $msg->dataMsg() . '</p>
                                        <p>
                                            <b>Mensagem:</b>
                                            <p>' . $msg->msg . '</p>
                                        </p>
                                    </div>';

            $data['box_mensagens'] = strip_tags($data['box_mensagens'], '<p><div><b><span><br>');
        } else {
            $data['box_mensagens'] = null;
        }

        return $data;
    }

    public function DialogMensagensEspeciais()
    {

        $data['header'] = '<i class="ion-wand" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Mensagens especiais';


        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        // SELECIONANDO AS DISCIPLINAS DO ANO CORRENTE

        $disciplinas = Disciplinas::where('ano_formacao_id', $id_ano_corrente)->get(['id']);
        foreach ($disciplinas as $disciplina) {
            $disciplinasIDs[] = $disciplina->id;
        }

        $disciplinasIDs = ($disciplinasIDs) ?? array();

        // SELECIONANDO AS AVALIAÇÕES REFERENTE AS DISCIPLINAS ACIMA SELECIONADAS

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasIDs)->where('data', '<', date('Y-m-d'))->orderBy('disciplinas_id', 'asc')->orderBy('data', 'desc')->get();
        $avaliacoes_select[] = '<select class="custom-select" name="avaliacaoID">';
        $avaliacoes_select[] = '<option value="0">Selecione uma avaliaçao</option>';
        foreach ($avaliacoes as $avaliacao) {
            $avaliacoes_select[] = '<option value="' . $avaliacao->id . '">' . $avaliacao->nome_abrev . ' - ' . $avaliacao->chamada . 'ª chamada de ' . $avaliacao->disciplinas->nome_disciplina_abrev . '</option>';
        }
        $avaliacoes_select[] = '</select>';

        $data['body'] = '   <p>Selecione a avaliação que você deseja enviar o resultado para os alunos. <span style="color: #696969;"><i>O resultado será enviado para todos os alunos selecionados que possuem nota nessa avaliação.</i></span></p>
                            <p style="color: #0B2161;"><b>Está ação pode demorar alguns minutos!</b></p>
                            <p>
                                ' . implode('', $avaliacoes_select) . '
                            </p>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="enviarMsgEspecialAluno(this);">
                                Enviar
                            </button>';
        return $data;
    }

    public function EnviarMensagemEspecialAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        if (isset($request->alunosCheck)) {
            // PEGANDO DADOS DA AVALIAÇÃO

            $avaliacao = Avaliacoes::find($request->avaliacaoID);

            $disciplina = $avaliacao->disciplinas->nome_disciplina;

            // SELECIONANDO NA TABELA avaliacoes_notas AS NOTAS DOS ALUNOS NA AVALIAÇÃO $request->avaliacaoID

            $avaliacoes_notas = AvaliacoesNotas::where('avaliacao_id', $request->avaliacaoID)->whereIn('alunos_id', $request->alunosCheck)->get();

            // GRAVANDO NUMA ARRAY AS NOTAS

            foreach ($avaliacoes_notas as $avaliacao_nota) {
                $nota_aluno[$avaliacao_nota->alunos_id] = array(
                    "gbo" => $avaliacao_nota->gbo,
                    "nota" => $avaliacao_nota->getNota()
                );
            }

            // SELECIONANDO TODOS OS ALUNOS QUE ESTAO EM $request->alunosCheck

            $alunos = Alunos::whereIn('id', $request->alunosCheck)->get();
            foreach ($alunos as $aluno) {
                $aluno_nome[$aluno->id] = $aluno->nome_guerra;
            }

            $operadorID = session()->get('login.operadorID');
            $operador = Operadores::find($operadorID);

            foreach ($request->alunosCheck as $alunoID) {
                // VERIFICO SE TEM NOTA DESSE ALUNO NESSA DISCIPLINA

                if (isset($nota_aluno[$alunoID])) {
                    $message = "    <b>Resultado de avaliação</b> - " . $avaliacao->nome_abrev . " - " . $disciplina . " - " . $avaliacao->chamada . "ª chamada
                                    <p>GRAU: " . $nota_aluno[$alunoID]['gbo'] . "/" . $avaliacao->gbm . "</p>
                                    <p>NOTA: " . $nota_aluno[$alunoID]['nota'] . "</p>
                                ";

                    $msg = new TelegramMsgEnviadas();
                    $msg->origem_operador_id = session()->get('login.operadorID');
                    $msg->destino_aluno_id = $alunoID;
                    $msg->usr_msg_destino = $aluno_nome[$alunoID];
                    $msg->usr_msg_origem = $operador->postograd->postograd_abrev . ' ' . $operador->nome_guerra;
                    $msg->data_hora = date('Y-m-d H:i:s');
                    $msg->msg = $message;
                    $msg->save();

                    // ENVIO DESTA MENSAGEM PARA O TELEGRAM DO ALUNO

                    $sendMsg = new TelegramSendPostController();

                    $sendMsg->sendTelegramMessage($alunoID, $message);
                }
            }
        }

        return 'ok';
    }
}
