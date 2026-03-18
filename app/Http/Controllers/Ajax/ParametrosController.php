<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Mail\VerificaBoletim;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\ConteudoAtitudinal;
use App\Models\MapaOutrosDados;
use App\Models\OMCT;
use App\Models\Parametros;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ParametrosController extends Controller
{
    protected $request;
    protected $ownauthcontroller;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->request = $request;
        $this->ownauthcontroller = $ownauthcontroller;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$parametros = Parametros::find($request->idParametro)) {
            $parametros = new Parametros();
        }

        switch($request->path()){
            case 'ajax/rod-conteudo-atitudinal':
                if(isset($request->conteudo)){//Caso conteúdo atitudinal

                    foreach($request->conteudo as $conteudo){
                        $conteudoAtitudinal[] = (int)$conteudo;
                    }

                    $parametros->ano_formacao_id = $request->anoFormacao;
                    $parametros->conteudo_atitudinal_rod = json_encode($conteudoAtitudinal);
            
                    $parametros->save();

                    $retorno['status'] = 'success';
                    $retorno['response'] = 'Conteúdos Atitudinais Gravados no Sistema.';
                }else{
                    $retorno['status'] = 'erro';
                    $retorno['response'] = 'Informe os Conteúdos Atitudinais.';
                }
            break;
            default:
                if(isset($request->textCandidatosAguardando)){//Caso Seja somente os candidatos aguardando aprovação    

                    $parametros->ano_formacao_id = $request->anoFormacao;
                    $parametros->candidato_aguar_aprov = $request->textCandidatosAguardando;
            
                    $parametros->save();
            
                    $retorno['status'] = 'success';
                    $retorno['response'] = 'Candidatos Gravado no Sistema.';
                }else{
                    $retorno['status'] = 'erro';
                    $retorno['response'] = 'Informe os Candidatos Que Estão Aguardando Aprovação.';
                }
            break;
        }

        return response()->json($retorno);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $id = explode('|', $id);

        switch ($id[0]) {
            case 'parametros':

                $data['response'] = '<form id="parametros_atalaia">
                                <input type="hidden" name="_token" value="' . csrf_token() . '"/>' .
                    FuncoesController::retornaBotaoAnoFormacao() .
                    '<div style="margin: 46px auto; text-align: center; " id="parametros-content"></div>';


                $data['response'] = $data['response'] . "<script>
                                        $(document).ready(function() {
                                            
                                            $('.btn.btn-secondary').click(function() {
                                                
                                                carregaOpcaoParametros('parametros', 'tela|'+$('input[name=\"ano_formacao\"]:checked').val());
                                                
                                            });
                                        });

                                        function carregaOpcaoParametros(tipo, item) {
                                            itemSplit = item.split('|');

                                            $.ajax({
                                                type: 'GET',
                                                dataType: 'json',
                                                url: '/ajax/' + tipo + '/' + item,
                                                beforeSend: function() {   
                                                    if(itemSplit[0] != 'grid' && itemSplit[0] != 'rod'){
                                                        $('div#parametros-content').empty();
                                                        $('div#parametros-content').html('<div id=\"temp\"><img src=\"/images/loadings/loading_01.svg\" style=\"width: 24px; margin-right: 8px;\" /> Aguarde, carregando...</div>');
                                                    }
                                                },
                                                success: function(data) {
                                                    if(itemSplit[0] == 'grid'){
                                                        $('div#tableInfo').remove();
                                                        $('div#divParametro').remove();
                                                        $('div#divROD').remove();
                                                        $('div#parametros-content').append(data.response);
                                                    }else if(itemSplit[0] == 'rod'){
                                                        $('div#parametros-content').append(data.response);
                                                    }else{
                                                        $('div#temp').fadeOut(300, function() {
                                                            $(this).remove();
                                                            $('div#parametros-content').empty();
                                                            $('div#parametros-content').html(data.response);

                                                            //carregaOpcaoParametros('parametros', 'grid|'+$('input[name=\"ano_formacao\"]:checked').val());
                                                        });
                                                    }
                                                },
                                                error: function(jqxhr) {
                                                    setTimeout(function() {
                                                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                                                    }, 1000);
                                                }
                                            });
                                        }

                                        function dialogRemoverMapaControle(id){
                                            $(document).confirmAcao('<strong>ATENÇÃO: </strong>.<p>Está certo disso? Os dados serão PERMANENTEMENTE perdido.</p>', function(){
                                                $.ajax({
                                                    type: 'GET',
                                                    dataType: 'json',
                                                    url: '/ajax/parametrosDeleteInfo/' + id,
                                                    success: function(data){
                                                        carregaOpcaoParametros('parametros', 'grid|'+$('input[name=\"ano_formacao\"]:checked').val());
                                                        
                                                    }
                                                });
                                            });
                                        } 
                                    </script>
                                    </form>";

                $data['modalTitle'] = 'Parâmetros';
                break;
            case 'tela':
                $anoFormacao = AnoFormacao::find($id[1]);

                $omcts = OMCT::where([['id', '<>', 1], ['id', '<>', 99]])->get(); //Diferente de ESA(1) e NE(99)
                foreach ($omcts as $omct) {
                    if ($this->ownauthcontroller->PermissaoCheck(1)) {
                        $options_omcts[] = $omct;
                    } else if (session()->get('login.omctID') == $omct->id) {
                        $options_omcts[] = $omct;
                    }
                }

                $comboUete = ['<option value="0" disabled selected hidden>UETE</option>'];
                foreach ($options_omcts as $omct) {
                    $comboUete[] = '<option value=' . $omct->id . ' >' . $omct->sigla_omct . '</option>';
                }

                $areas = Areas::where([['id', '<>', 5]])->get();

                $comboArea = ['<option value="0" disabled selected hidden>Área</option>'];
                foreach ($areas as $area) {
                    $comboArea[] = '<option value=' . $area->id . ' >' . $area->area . '</option>';
                }

                $data['response'] = '<div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc; padding-bottom:10px;">
                                        <h4 style="text-align: center; margin-bottom: 12px;">Mapa do Controle do Efetivo<b>' . $anoFormacao->formacao . '</b></h4>
                                    </div>
                                    <div>
                                        <label for="uete" class="custom-control-label" style="padding: 10px;width:150px;font-weight:bold;"> UETE 
                                            <select class="custom-select required_to_show_button" id="uete" name="uete" style="display:block;">
                                                ' . implode('', $comboUete) . '
                                            </select>
                                        </label>
                                        <label for="area" class="custom-control-label" style="padding: 10px;width:150px;font-weight:bold; display: none;" > Área 
                                            <select class="custom-select required_to_show_button" id="area" name="area">
                                                ' . implode('', $comboArea) . '
                                            </select>
                                        </label>
                                        <label for="segmento" class="custom-control-label" style="padding: 10px;width:140px;font-weight:bold; display: none;" > Segmento 
                                            <select class="custom-select required_to_show_button" id="segmento" name="segmento">
                                                <option value="M" selected>Masculino</option>
                                                <option value="F" >Feminino</option>
                                            </select>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">Previsto
                                            <input class="form-control" style="display:block;" name="previsto" autocomplete="off"/>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">Designado p/ Mtcl
                                            <input class="form-control" style="display:block;" name="desigMtcl" autocomplete="off"/>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">Adiamento de Mtcl
                                            <input class="form-control" style="display:block;" name="adiamMtcl" autocomplete="off"/>
                                        </label>

                                        <label class="custom-control-label" style="padding: 5px;width:5%;">1ª Matrícula
                                            <input class="form-control" style="display:block;" name="em_1mtcl" autocomplete="off"/>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">2ª Matrícula
                                            <input class="form-control" style="display:block;" name="em_2mtcl" autocomplete="off"/>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">Matrícula por Adiamento
                                            <input class="form-control" style="display:block;" name="em_mtcladiamento" autocomplete="off"/>
                                        </label>
                                        <label class="custom-control-label" style="padding: 5px;width:5%;">Matrícula por Ordem Judicial
                                            <input class="form-control" style="display:block;" name="em_mtclordjudicial" autocomplete="off"/>
                                        </label>
                                        <button id="submit-parametros" type="button" class="btn btn-primary" style="display: none;"">Aplicar Parâmetros</button>
                                    </div>';

                $data['response'] = $data['response'] . '<div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc; padding-bottom:10px;"></div>';

                $data['response'] = $data['response'] . "<script>
                                                            $(document).ready(function() {

                                                                $('select.required_to_show_button[name=uete]').on('change', function(){
                                                                    $('button#submit-parametros').hide();
                                                                    $('label[for=\"area\"]').show();
                                                                    $('select.required_to_show_button[name=area]').val(0)
                                                                    $('label[for=\"segmento\"]').hide();

                                                                    $('input[name=previsto]').val(null);
                                                                    $('input[name=desigMtcl]').val(null);
                                                                    $('input[name=adiamMtcl]').val(null);
                                                                    $('input[name=em_1mtcl]').val(null);
                                                                    $('input[name=em_2mtcl]').val(null);
                                                                    $('input[name=em_mtcladiamento]').val(null);
                                                                    $('input[name=em_mtclordjudicial]').val(null);
                                                                });

                                                                $('select.required_to_show_button[name=area]').on('change', function(){
                                                                    $('label[for=\"segmento\"]').show();
                                                                    carregaInfo();
                                                                });

                                                                $('select.required_to_show_button[name=segmento]').on('change', function(){
                                                                    carregaInfo();
                                                                });

                                                                $('button#submit-parametros').click(function(){
                                                                    updateInfo();
                                                                });

                                                                carregaOpcaoParametros('parametros', 'grid|'+$('input[name=\"ano_formacao\"]:checked').val());
                                                            });


                                                            function carregaInfo(){
                                                                var dados = new FormData(document.getElementById('parametros_atalaia'));
                                                                $.ajax({
                                                                    cache: false,
                                                                    type: 'POST',
                                                                    dataType: 'json',
                                                                    data: dados,
                                                                    url: '/ajax/parametrosInfo',
                                                                    processData: false, // tell jQuery not to process the data
                                                                    contentType: false, // tell jQuery not to set contentType
                                                                    success: function(data) {
                                                                        $('input[name=previsto]').val(data.qtdade_previstomtcl);
                                                                        $('input[name=desigMtcl]').val(data.qtdade_designadomtcl);
                                                                        $('input[name=adiamMtcl]').val(data.qtdade_adiamentomtcl);

                                                                        $('input[name=em_1mtcl]').val(data.qtdade_em_1mtcl);
                                                                        $('input[name=em_2mtcl]').val(data.qtdade_em_2mtcl);
                                                                        $('input[name=em_mtcladiamento]').val(data.qtdade_em_mtcladiamento);
                                                                        $('input[name=em_mtclordjudicial]').val(data.qtdade_em_mtclordjudicial);

                                                                        carregaOpcaoParametros('parametros', 'grid|'+$('input[name=\"ano_formacao\"]:checked').val());

                                                                        $('button#submit-parametros').show();
                                                                    },
                                                                    error: function(jqxhr) {
                                                                        setTimeout(function() {
                                                                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                                                                        }, 1000);
                                                                    }
                                                                    
                                                                });
                                                            }

                                                            function updateInfo(){
                                                                var dados = new FormData(document.getElementById('parametros_atalaia'));
                                                                $.ajax({
                                                                    cache: false,
                                                                    type: 'POST',
                                                                    dataType: 'json',
                                                                    data: dados,
                                                                    url: '/ajax/parametrosUpdateInfo',
                                                                    processData: false, // tell jQuery not to process the data
                                                                    contentType: false, // tell jQuery not to set contentType
                                                                    success: function(data) {
                                                                        carregaInfo();
                                                                    },
                                                                    error: function(jqxhr) {
                                                                        setTimeout(function() {
                                                                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                                                                        }, 1000);
                                                                    }
                                                                    
                                                                });
                                                            }
                                                        </script>";

                break;
            case 'grid':

                $anoFormacao = AnoFormacao::find($id[1]);

                $mapaOutrosDados = MapaOutrosDados::with('uete')->with('area')->where('ano_formacao_id', '=', $anoFormacao->id)->get();

                $parametros = Parametros::where('ano_formacao_id', '=', $anoFormacao->id)->first();

                foreach ($mapaOutrosDados as $mapa) {

                    $lista[] = '<tr>
                                    <td>' . $mapa->uete->sigla_omct . '</td>
                                    <td>' . $mapa->area->area . '</td>
                                    <td>' . (($mapa->sexo == 'M') ? 'Masculino' : 'Feminino') . '</td>
                                    <td>' . $mapa->qtdade_previstomtcl . '</td>
                                    <td>' . $mapa->qtdade_designadomtcl . '</td>
                                    <td>' . $mapa->qtdade_adiamentomtcl . '</td>
                                    <td>' . $mapa->qtdade_em_1mtcl . '</td>
                                    <td>' . $mapa->qtdade_em_2mtcl . '</td>
                                    <td>' . $mapa->qtdade_em_mtcladiamento . '</td>
                                    <td>' . $mapa->qtdade_em_mtclordjudicial . '</td>
                                    <td><a href="javascript: void(0);" class="no-style" onclick="dialogRemoverMapaControle(' . $mapa->id . ');" title="Remover Registro definitivamente do sistema"> <i class="ion-android-delete" style="font-size: 22px;"></i> </a></td>
                                </tr>';
                }

                if (!isset($lista)) {
                    $lista[] = '<tr><td colspan="11">Sem Ocorrências</td></tr>';
                }

                $data['response'] = '<div id="tableInfo" style="width: 90%; margin: 22px auto; text-align: center;">
                                                        <table class="table table-striped" style="margin: 60px 0 90px 0;">
                                                            <thead>
                                                                <tr>
                                                                    <th>UETE</th>
                                                                    <th>Área</th>
                                                                    <th>Segmento</th>
                                                                    <th>Previsto</th>
                                                                    <th>Designado p/ Mtcl</th>
                                                                    <th>Adiamento de Mtcl</th>
                                                                    <th>1ª Matrícula</th>
                                                                    <th>2ª Matrícula</th>
                                                                    <th>Matrícula por Adiamento</th>
                                                                    <th>Matrícula por Ordem Judicial</th>
                                                                    <th>Açôes</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            ' . implode('', $lista) . '
                                                            </tbody>
                                                        </table>
                                                       </div>';

                $data['response'] = $data['response'] . view('admin.parametros.candidatosAguardandoAprov', compact('parametros'));
                break;
            case 'rod':
                $anoFormacao = AnoFormacao::find($id[1]);
                $parametros = Parametros::where('ano_formacao_id', '=', $anoFormacao->id)->first();

                $conteudoAtitudinal = ConteudoAtitudinal::all();

                $data['response'] = '' . view('admin.parametros.parametroROD', compact('parametros', 'conteudoAtitudinal'));
                break; 
            default:
                break;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadInfo(Request $request)
    {
        $mapaOutrosDados = MapaOutrosDados::firstOrCreate(['ano_formacao_id' => $request->ano_formacao, 'omct_id' => $request->uete, 'area_id' => $request->area, 'sexo' => $request->segmento]);

        return response()->json($mapaOutrosDados);
    }

    public function updateInfo(Request $request)
    {

        $mapaOutrosDados = MapaOutrosDados::updateOrCreate(
            ['ano_formacao_id' => $request->ano_formacao, 'omct_id' => $request->uete, 'area_id' => $request->area, 'sexo' => $request->segmento],
            [
                'qtdade_previstomtcl' => (isset($request->previsto) ? $request->previsto : 0), 'qtdade_designadomtcl' => (isset($request->desigMtcl) ? $request->desigMtcl : 0), 'qtdade_adiamentomtcl' => (isset($request->adiamMtcl) ? $request->adiamMtcl : 0), 'qtdade_em_1mtcl' => (isset($request->em_1mtcl) ? $request->em_1mtcl : 0), 'qtdade_em_2mtcl' => (isset($request->em_2mtcl) ? $request->em_2mtcl : 0), 'qtdade_em_mtcladiamento' => (isset($request->em_mtcladiamento) ? $request->em_mtcladiamento : 0), 'qtdade_em_mtclordjudicial' => (isset($request->em_mtclordjudicial) ? $request->em_mtclordjudicial : 0)
            ]
        );
        return response()->json($mapaOutrosDados);
    }

    public function deleteInfo(Request $request)
    {
        $mapaOutrosDados = MapaOutrosDados::find($request->id);
        $mapaOutrosDados->delete();

        return response()->json($mapaOutrosDados);
    }
    
    public function sendMailBoletim(){
        
        $email = $this->request->email;
        $militar = $this->request->militar;

        if (empty($email) || empty($militar)) {
            return false;
        }
        
        try {
            Mail::to($email)->send(new VerificaBoletim($militar));
            return true;

        } catch (\Exception $e) {

            Log::error('Erro ao enviar email militar: '.$e->getMessage());
            return false;
        }
    }
}
