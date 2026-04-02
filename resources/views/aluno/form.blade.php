@include('aluno.partials._styles')

<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Implantar Aluno
        <div style="float: right">
            @include('aluno.pesquisaAluno')
        </div>
    </div>
    <div class="card-body"
    @if(isset($aluno))
        style='background-color: rgb(237,236,228);';
    @endif
    
        <div class="alert alert-success success-implantar-aluno" role="alert" style="margin-bottom:10px;"></div>
        <div class="alert alert-danger errors-implantar-aluno erro-upload" role="alert" style="margin-bottom:10px;"></div>
        <form id="implantar_aluno">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" id="aluno_imagem" name="aluno_imagem" style="display: none;">
            <div class="nav new-nav-tabs" id="nav-tab" role="tablist">
                <a class="new-nav-item new-nav-link active" data-toggle="tab" href="#nav-implantar-aluno1" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Matrícula no CFGS</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno2" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Primeiro Ano CFGS</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno3" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Situação Militar ou Civil Anterior</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno4" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Endereço Residencial Atual</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno5" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Documentação</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno6" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Dados Pessoais</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno7" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Dependentes</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno8" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Situação Sócio-Econômica</font>
                </a>
                <!--<a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno9" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Dados Bancários</font></a>-->
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno10" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Fardamento</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno11" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Observações</font>
                </a>
            </div>
            <div class="tab-content" id="nav-tabContent">
                @include('aluno.partials.tab_matricula')
                @include('aluno.partials.tab_primeiro_ano')
                @include('aluno.partials.tab_situacao_anterior')
                @include('aluno.partials.tab_endereco')
                @include('aluno.partials.tab_documentacao')
                @include('aluno.partials.tab_dados_pessoais')
                @include('aluno.partials.tab_dependentes')
                @include('aluno.partials.tab_socioeconomica')
                @include('aluno.partials.tab_fardamento')
                @include('aluno.partials.tab_observacoes')
            </div>
        </form>
    </div>
</div>

@include('aluno.partials._actions')
@include('aluno.partials._scripts')