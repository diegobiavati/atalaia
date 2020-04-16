<style>
    div.link_rapido_menu {
        display: none;
    }

    p.card-text {
        color: #FF4000;
    }
</style>
<div style="text-align: center; margin-bottom: 56px; color: #696969;">
    <h4>VISÃO GERAL DO SISTEMA</h4>
</div>
<div class="card-deck">
    <div class="card">
        <div style="background-color: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-ios-calendar-outline" style="font-size: 44px;"></i><br />
                <h5>ANOS DE FORMAÇÃO</h5>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title"><b>Ano de formação corrente</b></h5>
            @if(isset($ano_corrente->formacao))
            <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
                {{$ano_corrente->formacao}}<br /> <span style="font-size: 16px;">com data de matrícula para:
                    <br /><b>{{strftime('%d-%m-%Y', strtotime($ano_corrente->data_matricula))}}</b>
                </span>
            </p>
            @else
            <p class="card-text">
                Não há ano de formação cadastrado
            </p>
            @endif
        </div>
    </div>
    <div class="card">
        <div style="background-color: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-android-contacts" style="font-size: 44px;"></i><br />
                <h5>ALUNOS</h5>
            </div>
        </div>
        <div class="card-body">
            @if($ownauthcontroller->PermissaoCheck(10))
            <div class="link_rapido_menu" style="position: absolute; width: 56px; height: 56px; right: 5%; margin-top: -48px; border-radius: 50%; background-color: #fff; box-shadow: 1px 1px 8px #696969; text-align: center; padding-top: 10px;">
                <a href="javascript: void:(0);" class="no-style" style="font-size: 24px;" onclick="$('a#alunos').click();"><i class="ion-edit"></i></a>
            </div>
            @endif
            <h5 class="card-title"><b>Efetivo pronto</b></h5>
            <div class="card-text" style="width: 80%; margin: 0 auto;">
                Segmento masculino <i>({{$alunos['total_alunos']}})</i><br />
                <div id="bar_porcentagem_alunos" style="background-color: #088A29; width: 0; height: 22px; float: left; margin-right: 6px; margin-top: 2px;"></div>
                <span id="porcentagem_seg_masc" style="display: none;">{{$alunos['porcentagem_alunos']}}%</span>
                <div class="clear"></div>
                Segmento feminino <i>({{$alunos['total_alunas']}})</i><br />
                <div id="bar_porcentagem_alunas" style="background-color: #FA58AC; width: 0; height: 22px; float: left; margin-right: 6px; margin-top: 2px;"></div>
                <span id="porcentagem_seg_fem" style="display: none;">{{$alunos['porcentagem_alunas']}}%</span>
                <div class="clear"></div>
            </div>
        </div>
    </div>

    @if(in_array(2, $funcaoOperador))
    <!--Só libera se for Cmt de Cia-->
    <div class="card">
        <div style="background-color: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-android-warning" style="font-size: 44px;"></i><br />
                <h5>FATOS OBSERVADOS</h5>
            </div>
        </div>
        <div class="card-body">

            <h5 class="card-title"><b>Resolvidos</b></h5>
            <div class="card-text" style="width: 80%; margin: 40px auto;">
                <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
                    {{ $lancamentoFo->where('providencia', '<>', null)->count() .'/'. $lancamentoFo->count() }}
                </p>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    @endif

    @if(in_array(4, $funcaoOperador))
    <!--Só libera se for Sargenteante da UETE-->
    <div class="card">
        <div style="background-color: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-android-warning" style="font-size: 44px;"></i><br />
                <h5>FATD</h5>
            </div>
        </div>
        <div class="card-body">

            <h5 class="card-title"><b>Pendentes</b></h5>
            <div class="card-text" style="width: 80%; margin: 40px auto;">
                <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
                    {{ $fatd->count() }}
                </p>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="card-deck" style="margin-top: 36px;">
    <div class="card">
        <div style="background: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-ios-bookmarks" style="font-size: 44px;"></i><br />
                <h5>DISCIPLINAS</h5>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title"><b>Cadastradas</b></h5>
            <p class="card-text">
                @forelse($disciplinas as $disciplina)
                <li style="color: #FF4000;">{{$disciplina->nome_disciplina_abrev}}</li>
                @empty
                Não á disciplinas cadastradas
                @endforelse
            </p>
        </div>
    </div>
    <div class="card">
        <div style="background-color: #088A4B; border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-ios-compose" style="font-size: 44px;"></i><br />
                <h5>AVALIAÇÕES</h5>
            </div>
        </div>
        <div class="card-body">
            @if($ownauthcontroller->PermissaoCheck(3))
            <div class="link_rapido_menu" style="position: absolute; width: 56px; height: 56px; right: 5%; margin-top: -48px; border-radius: 50%; background-color: #fff; box-shadow: 1px 1px 8px #696969; text-align: center; padding-top: 10px;">
                <a href="javascript: void:(0);" class="no-style" style="font-size: 24px;" onclick="$('a#avaliacoes').click();"><i class="ion-edit"></i></a>
            </div>
            @endif
            <h5 class="card-title"><b>Agenda - próximas avaliações</b></h5>
            <p class="card-text">
                @if(count($avaliacoes)>0)
                @foreach($avaliacoes as $avaliacao)
                <li style="color: #FF4000;"><b>{{$avaliacao->nome_completo}} </b> de <span style="color: #696969;"><i>{{$avaliacao->disciplinas->nome_disciplina_abrev}}</i></span> ({{strftime('%A, %d de %B de %Y às %H:%M', strtotime($avaliacao->data.' '.$avaliacao->hora))}})</li>
                @endforeach
                @else
                Sem avaliações nos próximos dias
                @endif
            </p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('div#bar_porcentagem_alunos').animate({
            width: "{{$alunos['porcentagem_alunos'] +1}}%",
        }, 600, function() {
            $('span#porcentagem_seg_masc').fadeIn();
        });
        $('div#bar_porcentagem_alunas').animate({
            width: "{{$alunos['porcentagem_alunas']+1}}%",
        }, 600, function() {
            $('span#porcentagem_seg_fem').fadeIn();
        });

    });

    $(document).on('mouseover', 'div.card', function() {
        $(this).find('div.link_rapido_menu').fadeIn(200);
    }).on('mouseleave', 'div.card', function() {
        $(this).find('div.link_rapido_menu').fadeOut(200);
    });
</script>