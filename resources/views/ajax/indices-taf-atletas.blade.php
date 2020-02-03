<style>
    a.nav-link{
        color: #A4A4A4;
    }
    tr:nth-child(even) {
        background: #F2F2F2;

    }
    
</style>
<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-android-walk"></i><strong>Índices do TAF e atletas</strong>
        <div style="float: right">
            <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                <a class="no-style" href="javascript: void(0);">
                        <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div-->
            <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                <i class="ion-android-more-vertical" style="color: #696969;"></i>
            </a>
            <div id="popover-content" style="display: none;">
                <div class="menu_inside_popover">
                    <i class="ion-arrow-graph-up-right"></i><a href="javascript: void(0);" onclick="dialogAdicionarPortaria();">Criar nova Portaria/índices</a><br />                 
                </div>
                <!--div class="menu_inside_popover">
                    <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                </div-->                                            
            </div>  
        </div>
    </div>

    <div class="card-body">
        <div class="alert alert-danger errors-adicionar-portaria2" role="alert"></div>
        <!--h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;"></h5-->           
        <p class="card-text">
            @if(count($portarias)>0)
                <div style="width: 50%; margin: 0 auto;">
                    <select class="custom-select" name="portaria_id" onchange="loadConfigPortaria(this);">
                        <option value="0">Selecione uma portaria</option>
                        @foreach($portarias as $portaria)
                            <option value="{{$portaria->id}}">{{$portaria->nome_portaria}}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="box-registro-not-found">
                    <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                    <span style="color: brown">Não há portarias cadastradas</span>
                </div>
            @endif            
        </p>

        <div id="box-content-portarias"></div>

    </div>
    
</div>

<script>

    $("[data-toggle=popover]").popover({
            trigger: 'focus',
            html: true, 
            delay: { "show": 100, "hide": 400 },
            content: function() {
                return $('#popover-content').html();
            }
    }); 

</script>