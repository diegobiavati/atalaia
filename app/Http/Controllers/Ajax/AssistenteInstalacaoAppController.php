<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssistenteInstalacaoAppController extends Controller
{
    public function LoadCarouselAssistente(){

        $data = '<h3 style="text-align: center;">ASSISTENTE DE CONFIGURAÇÃO</h3>
                 <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="margin: 26px;">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_01.jpg" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    O primeiro passo é a instalação do aplicativo Telegram no seu SmartPhone.<br />
                                    O aplicativo está disponível para Android, IOs e WindowsPhone.
                                </p>
                            </div>                            
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_02.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Após a instalação e configuração inicial do aplicativo, na tela de contatos toque na <i>LUPA</i> para localizar<br />
                                </p>
                            </div>    
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_03.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Digite então <i>AtalaiaPB</i> como mostra a figura na marcação 1.<br />
                                    Assim que o BOT é localizado, toque sobre ele como mostrado na marcação 2.
                                </p>
                            </div>    
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_04.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Ok! Se tudo deu certo você deve ter chegado nessa tela. Toque em INICIAR como mostra a figura.
                                </p>
                            </div>    
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_05.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Nesse passo, você deve somente digitar o seu email, assim como mostra figura.
                                </p>
                            </div>    
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_06.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Se seu e-mail estiver correto, você receberá uma saudação. Será preciso responder a mensagem informando seu
                                    <i>passaporte</i>. Proceda como mostra a figura.
                                </p>
                            </div>    
                        </div>
                        <div class="carousel-item" style="width: 800px; height: 432px; margin: 0 auto; text-align: center;">
                            <img class="" src="/images/assistente_instalacao_app/telegram_07.jpg" style="height: 306px;" alt="First slide">
                            <div class="carousel-caption d-none d-md-block">
                                <p>
                                    Informe seu <i>passaporte</i> que está na tela de boas-vindas deste assistente e aguarde...
                                </p>
                            </div>    
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                    </a>
                </div>';

        return $data;

    }

}
