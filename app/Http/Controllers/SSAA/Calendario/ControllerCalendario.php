<?php

namespace App\Http\Controllers\SSAA\Calendario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Models\AnoFormacao;
use App\Models\EsaAvaliacoes;
use Illuminate\Http\Request;

class ControllerCalendario extends Controller
{
    private $_ownauthcontroller = null;
    private $_request = null;
   
    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    public function index()
    {
        if(isset($this->_request->id_ano_formacao)){
            $anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);
            
            $encrypt = encrypt(session('_token').'-'.$anoFormacao->id);
            session()->put('_anoFormacao', $encrypt);

            $month = $anoFormacao->formacao.'-'.date("m");

            $data = $this->calendar_month($month);
            $mes = $data['month'];
            
            $mesportuguese = $this->portuguese_month($mes);
            $mes = $data['month'];

            return view("ssaa.calendario.calendario", [
                'data' => $data,
                'mes' => $mes,
                'encrypt' => $encrypt,
                'anoFormacao' => $anoFormacao,
                'ownauthcontroller' => $this->_ownauthcontroller,
                'mesportuguese' => $mesportuguese
            ]);
        }

        return view("ssaa.calendario.index");
    }

    public function index_mes($id_ano_formacao, $mes, OwnAuthController $ownauthcontroller)
    {
        $anoFormacao = AnoFormacao::find($id_ano_formacao);

        if($anoFormacao->formacao == explode('-', $mes)[0]){
            $data = $this->calendar_month($mes);
            $mes = $data['month'];

            $mesportuguese = $this->portuguese_month($mes);
            $mes = $data['month'];

            return view("ssaa.calendario.calendario", [
                'data' => $data,
                'mes' => $mes,
                'anoFormacao' => $anoFormacao,
                'ownauthcontroller' => $ownauthcontroller,
                'mesportuguese' => $mesportuguese
            ]);
        }else{
            return redirect(session('url_anterior'));
        }
    }

    public static function calendar_month($month)
    {
        $mes = $month;
        //sacar el ultimo de dia del mes
        $daylast =  date("Y-m-d", strtotime("last day of " . $mes));
        //sacar el dia de dia del mes
        $fecha      =  date("Y-m-d", strtotime("first day of " . $mes));
        $daysmonth  =  date("d", strtotime($fecha));
        $montmonth  =  date("m", strtotime($fecha));
        $yearmonth  =  date("Y", strtotime($fecha));
        // sacar el lunes de la primera semana
        $nuevaFecha = mktime(0, 0, 0, $montmonth, $daysmonth, $yearmonth);
        $diaDeLaSemana = date("w", $nuevaFecha);
        $nuevaFecha = $nuevaFecha - ($diaDeLaSemana * 24 * 3600); //Restar los segundos totales de los dias transcurridos de la semana
        $dateini = date("Y-m-d", $nuevaFecha);
        //$dateini = date("Y-m-d",strtotime($dateini."+ 1 day"));
        // numero de primer semana del mes
        $semana1 = date("W", strtotime($fecha));
        // numero de ultima semana del mes
        $semana2 = date("W", strtotime($daylast));
        // semana todal del mes
        // en caso si es diciembre
        if (date("m", strtotime($mes)) == 12) {
            $semana = 5;
        } else {
            $semana = ($semana2 - $semana1) + 1;
        }
        // semana todal del mes
        $datafecha = $dateini;
        $calendario = array();
        $iweek = 0;

        $events = EsaAvaliacoes::whereBetween('realizacao', [$fecha, $daylast])->get();

        $param['qms_matriz'] = session('login.qmsID.0.qms_matriz_id');

        while ($iweek < $semana) :
            $iweek++;
            //echo "Semana $iweek <br>";
            //
            $weekdata = [];
            for ($iday = 0; $iday < 7; $iday++) {
                // code...
                $datafecha = date("Y-m-d", strtotime($datafecha . "+ 1 day"));
                $datanew['mes'] = date("M", strtotime($datafecha));
                $datanew['dia'] = date("d", strtotime($datafecha));
                $datanew['realizacao'] = $datafecha;
                //AGREGAR CONSULTAS EVENTO
                //$datanew['evento'] = EsaAvaliacoes::where("realizacao", $datafecha)->get();

                $param['datafecha'] = $datafecha;
                
                    $datanew['evento'] = $events->filter(function($avaliacao) use($param){
                        if($avaliacao->esadisciplinas->qms->qms_matriz_id == $param['qms_matriz']
                                && $avaliacao->realizacao == $param['datafecha']){
                            return $avaliacao;
                        }else if($param['qms_matriz'] == 9999 && $avaliacao->realizacao == $param['datafecha']){
                            return $avaliacao;
                        }
                    });

                array_push($weekdata, $datanew);
            }
            $dataweek['semana'] = $iweek;
            $dataweek['datos'] = $weekdata;
            //$datafecha['horario'] = $datahorario;
            array_push($calendario, $dataweek);
        endwhile;
        $nextmonth = date("Y-M", strtotime($mes . "+ 1 month"));
        $lastmonth = date("Y-M", strtotime($mes . "- 1 month"));
        $month = date("M", strtotime($mes));
        $yearmonth = date("Y", strtotime($mes));
        //$month = date("M",strtotime("2019-03"));
        $data = array(
            'next' => $nextmonth,
            'month' => $month,
            'year' => $yearmonth,
            'last' => $lastmonth,
            'calendar' => $calendario,
        );
        return $data;
    }

    public static function portuguese_month($month)
    {

        $mes = $month;
        if ($month == "Jan") {
            $mes = "Janeiro";
        } elseif ($month == "Feb") {
            $mes = "Fevereiro";
        } elseif ($month == "Mar") {
            $mes = "Março";
        } elseif ($month == "Apr") {
            $mes = "Abril";
        } elseif ($month == "May") {
            $mes = "Maio";
        } elseif ($month == "Jun") {
            $mes = "Junho";
        } elseif ($month == "Jul") {
            $mes = "Julho";
        } elseif ($month == "Aug") {
            $mes = "Agosto";
        } elseif ($month == "Sep") {
            $mes = "Setembro";
        } elseif ($month == "Oct") {
            $mes = "Outubro";
        } elseif ($month == "Nov") {
            $mes = "Novembro";
        } elseif ($month == "Dec") {
            $mes = "Dezembro";
        } else {
            $mes = $month;
        }
        return $mes;
    }

    /*public function details($id)
    {

        $event = Event::find($id);

        return view("ssaa.calendar.evento.evento", [
            "event" => $event
        ]);
    }*/
}
