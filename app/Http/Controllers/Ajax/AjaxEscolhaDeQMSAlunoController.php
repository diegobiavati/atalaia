<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;

/* MODELS */

use App\Models\Alunos;
use App\Models\EscolhaQMS;
use App\Models\EscolhaQMSAlunosOpcoes;
use App\Models\QMS;

class AjaxEscolhaDeQMSAlunoController extends Controller
{
    protected $escolha_qms_opcoes;
    protected $OwnAuth;
    protected $classLog;

    public function __construct(EscolhaQMSAlunosOpcoes $escolha_qms_opcoes, OwnAuthController $ownauth, \App\Http\OwnClasses\ClassLog $classLog){
        $this->escolha_qms_opcoes = $escolha_qms_opcoes;
        $this->OwnAuth = $ownauth;
        $this->classLog = $classLog;
        $classLog->ip=(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: null); 
    }

    public function DialogEscolhadeQMSAluno(Request $request){
        if($this->OwnAuth->PermissaoCheck('47un0') && $request->userID==auth()->id()){
            $email = auth()->user()->email;
            $aluno = Alunos::where('email', $email)->first();
            if($request->alunoID==$aluno->id){
                if($aluno->area_id==1){

                    // VERIFICANDO SITUAÇÃO DO ALUNO NA TABELA DE CLASSIFICAÇÃO

                    //if(isset($aluno->classificacao->reprovado) && $aluno->classificacao->reprovado=='N'){
                    if(isset($aluno->classificacao->reprovado)){

                        // VERIFICANDO SE HÁ ESCOLHA DE QMS DISPONÍVEL

                        $escolha_periodo = DB::select("SELECT * FROM escolha_qms WHERE data_hora_inicio<'".date('Y-m-d H:i:s')."' AND data_hora_fim>'".date('Y-m-d H:i:s')."'");
                        
                        if(count($escolha_periodo)==1){
                            
                            $data[] = ' <div style="text-align: center; margin-top: 8px; color: #ffffff;">
                                            <h5>
                                                '.$aluno->nome_guerra.', aqui você irá informar quais são suas prioridades de ingresso nas QMS disponíveis.<br />
                                                Para isso, clique na priodade e selecione a QMS correspondente.<br />
                                                Após preenchidas todas a opções, você deve clicar em <span style="color: #FF8000;"><i>Salvar opçoes</i></span>

                                            </h5> 
                                        </div>';

                            if($aluno->sexo=='M'){

                                // SELECIONANDO AS QMS DE ACORDO COM O SEGMENTO DO ALUNO

                                $qms = QMS::where('escolha_qms_id', $escolha_periodo[0]->id)->where('segmento', 'M')->where('qms_alias','<>', 'aviacao')->get();
                                
                            } else {

                                // SELECIONANDO AS QMS DE ACORDO COM O SEGMENTO DO ALUNO

                                $qms = QMS::where('escolha_qms_id', $escolha_periodo[0]->id)->where('segmento', 'F')->where('qms_alias','<>', 'aviacao_feminino')->get();

                            }

                            foreach($qms as $qms_data){
                                $qms_data_array[$qms_data->id] = array(
                                    'nome'=>$qms_data->qms,
                                    'img'=>$qms_data->img

                            );
                            }

                            // SELECIONANDO OPCOES ESCOLHIDAS SE EXISTIREM

                            $escolha_realizada = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $escolha_periodo[0]->id)->where('aluno_id', $aluno->id)->first();
                            
                            // CASO NÃO EXISTA, CRIO UMA NOVA!
                            
                            if(!$escolha_realizada){
                                $novas_opcoes_aluno = new EscolhaQMSAlunosOpcoes;
                                $novas_opcoes_aluno->aluno_id = $aluno->id;
                                $novas_opcoes_aluno->escolha_qms_id = $escolha_periodo[0]->id;
                                $novas_opcoes_aluno->save();

                            } else {
                                if($escolha_realizada->opcoes==null){
                                    foreach($qms as $qms_opcoes) {
                                        $opcoes[] = '   <div class="qms_option_class qms_id_'.$qms_opcoes->id.'" style="margin: 6px 2px; padding: 2px; border-bottom: 1px solid #E6E6E6;">
                                                            <a href="#" class="no-style2" onclick="selecionarOpcaoAluno(this, '.$qms_opcoes->id.');">
                                                                <img src="'.$qms_opcoes->img.'" style="width: 32px; vertical-align: middle; margin-right: 6px;" />'.$qms_opcoes->qms.'
                                                            </a>
                                                        </div>';
                                    }
        
                                    $data[] = ' <div style="text-align: center; margin-top: 20px;">
                                                    <form method="post" name="escolha_qms_opcoes">
                                                        <input type="hidden" name="_token" value="'.csrf_token().'">
                                                        <input type="hidden" name="aluno_id" value="'.$aluno->id.'">
                                                        <input type="hidden" name="escolha_qms_id" value="'.$escolha_periodo[0]->id.'">';
                                                        for($i=1;$i<=count($qms);$i++){
                                                            $data[] = ' <div id="prioridade_'.$i.'" class="btn-group">
                                                                            <div style="margin-bottom: 2px;">
                                                                                <a href="javascript: void(0);" class="no-style" id="prioridade_'.$i.'_preenchida" onclick="desfazerOpcaoAluno(this, \'prioridade_'.$i.'\', \''.$i.'\');" style="display: none;" title="Refazer prioridade">'.$i.'ª prioridade - </a>
                                                                            </div>
                                                                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-bottom: 6px; width: 180px;">
                                                                                '.$i.'ª prioridade
                                                                            </button>
                                                                            <input type="hidden" name="prioridade_'.$i.'" />
                                                                            <div class="dropdown-menu" style="width: 380px; margin-left: 180px; font-size:12px; background-color: rgba(255,255,255,0.975)">
                                                                                '.implode('', $opcoes).'
                                                                            </div>
                                                                        </div><br />';
                                                        }
                                    $data[] = '     </form>
                                                    <div id="confirm_checkbox_escolha_qms" class="form-check" style="display: none; background-color: #0A2A12; width: 400px; margin: 3px auto; border: 1px solid #CEF6D8; padding: 3px;">
                                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" onclick="$(\'span#salvar-opcoes\').slideToggle();">
                                                        <label class="form-check-label" for="defaultCheck1">
                                                            Minhas opções estão corretas. Quero registra-las!
                                                        </label>
                                                    </div>   
                                                </div>';
                                } else {
                                    $escolhas = unserialize($escolha_realizada->opcoes);
                                    $data[] = '<div style="margin-top: 18px; text-align: center;">';
                                    for($i=1;$i<=20;$i++){
                                        if(isset($escolhas['prioridade_'.$i])) {
                                            $data[] = '<div style="text-align: center;">'.$i.'ª Prioridade - <img src="'.$qms_data_array[$escolhas['prioridade_'.$i]]['img'].'" style="width: 32px; vertical-align: middle; margin-right: 6px;">'.$qms_data_array[$escolhas['prioridade_'.$i]]['nome'].'</div>';
                                        }
                                    }
                                    $data[] = '<button onclick="limparOpcoes('.$escolha_periodo[0]->id.', '.$aluno->id.');" type="button" class="btn btn-danger" style="margin-top: 12px;"><i class="ion-trash-a" style="font-size: 18px; margin-right: 10px;"></i>Limpar opçoes</button>';
                                    $data[] = '</div>';
                                } 
                            }

                        } else {
                            $data[] = ' <div style="text-align: center; margin-top: 240px; color: #FFBF00;">
                                            <h4>
                                                Não há período de escolha disponível!
                                            </h4>
                                        </div>';                            
                        }

                    } else {
                        $data[] = ' <div style="text-align: center; margin-top: 240px; color: #FFBF00;">
                                        <h4>
                                            Você não se enquandra no universo de escolha de QMS. Verifique sua condição de aprovação no Básico CFS. 
                                        </h4>
                                    </div>';
                    }

                } else {
                    $data[] = ' <div style="text-align: center; margin-top: 240px; color: #FFBF00;">
                                    <h4>
                                        Alunos da área de '.$aluno->area->area.' não se enquadram no universo de escolha de QMS. 
                                    </h4>
                                </div>';                    
                }

            } else {
                $data[] = ' <div style="text-align: center; margin-top: 240px;">
                                <h4>
                                    NÃO AUTORIZADO!<br />REFAÇA SEU LOGIN E TENTE NOVAMENTE
                                </h4>
                            </div>';
            }
        } else {
            $data[] = ' <div style="text-align: center; margin-top: 240px;">
                            <h4>
                                NÃO AUTORIZADO!<br />REFAÇA SEU LOGIN E TENTE NOVAMENTE
                            </h4>
                        </div>';
        }

        return implode('', $data);
    }

    public function GravarOpcoesAluno(Request $request){
        
        // SELECIONANDO O ID DO USER 

        $email = auth()->user()->email;
        $aluno = Alunos::where('email', $email)->first();

        // SELECIONANDO A ESCOLHA DE QMS PARA VALIDAR 
        
        /*
        
            O aluno só conseguirá realizar a alteração se a ESCOLHA DE QMS estiver no prazo de alteração
            e o $request->escolha_qms_id peretencer a exatamente esta QMS.

        */

        // VERIFICANDO QUANTAS OPÇÕES TEM PARA CADA SEGMENTO

        $qtde_opcoes = QMS::where('segmento', $aluno->sexo)->where('escolha_qms_id', $request->escolha_qms_id)->count();

        $qtde_opcoes = ($qtde_opcoes)??0;

        $escolha_qms = EscolhaQMS::where('data_hora_inicio', '<', date('Y-m-d H:i:s'))->where('data_hora_fim', '>', date('Y-m-d H:i:s'))->where('id', $request->escolha_qms_id)->first();

        if($aluno->id==$request->aluno_id && $escolha_qms){

            // CONTANDO OS REGISTRO

            $escolha_bug = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $request->escolha_qms_id)->where('aluno_id', $request->aluno_id)->count();

            if($escolha_bug>1){

                // E EXCLUINDO SFC

                $escolha_bug = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $request->escolha_qms_id)->where('aluno_id', $request->aluno_id)->get();
                $escolha_bug->delete();

                // CRIANDO UMA NOVA

                $novas_opcoes_aluno = new EscolhaQMSAlunosOpcoes;
                $novas_opcoes_aluno->aluno_id = $aluno->id;
                $novas_opcoes_aluno->escolha_qms_id = $request->escolha_qms_id;
                $novas_opcoes_aluno->save();

            }

            $update_escolha = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $request->escolha_qms_id)->where('aluno_id', $request->aluno_id)->first();

            for($i=1;$i<$qtde_opcoes;$i++){
                if(isset($request['prioridade_'.$i])){
                    $escolha['prioridade_'.$i] =  $request['prioridade_'.$i];
                }
            }

            $update_escolha->finalizada = (count($escolha)==($qtde_opcoes -1))? 'S':'N';

            $update_escolha->opcoes = serialize($escolha);
            $update_escolha->save();
        
            $data['alunoID'] = $request->aluno_id;
            $data['userID'] = auth()->id();
            $data['status'] = 'ok';
            $data['content'] = '<div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">
                                    <b>PARABÉNS!</b>
                                </div>
                                <div style="padding: 4px 18px;">
                                    Suas opções foram cadastadas com sucesso. Agora, é só aguardar!<p>Se você quiser refazê-las, basta clicar no botão <i>Limpar opções</i></p>
                                </div>
                                <div style="padding: 8px 24px; text-align: right;">
                                    <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">
                                        <b>OK</b>
                                    </a>
                                </div>';
        
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Aluno gravou suas escolhas de QMS', auth()->user()->email);
        return $data;
    }

    public function LimparOpcoesAluno(Request $request){
        
        // SELECIONANDO O ID DO USER 

        $email = auth()->user()->email;
        $aluno = Alunos::where('email', $email)->first();

        if($aluno->id==$request->aluno_id){
            $update_escolha = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $request->escolha_qms_id)->where('aluno_id', $request->aluno_id)->first(); 
            $update_escolha->opcoes = null; 
            $update_escolha->finalizada = 'N'; 
            $update_escolha->save();
        
            $data['alunoID'] = $request->aluno_id;
            $data['userID'] = auth()->id();
            $data['status'] = 'ok';
        
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Aluno limpou suas escolhas de QMS', auth()->user()->email);
        return $data;
    }
}
