<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramSendPostController extends Controller
{
    public function sendTelegramMessage($alunoID, $message)
    {

        $data = array(
            "alunoID" => $alunoID,
            "msg" => $message,
            "api_key" => '76D45842B52D5CCB14055BE876D605D031F11B9269CAA24F3BB28EED21E75AB2'
        );

        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://wservice.esa.eb.mil.br/e1a240695dcf78312267e5ade7079eb665a24fdb/post_msg.php');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $resp = curl_exec($curl);
    }
}
