<style>
    .aluno-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .aluno-card-header {
        background: linear-gradient(90deg, #f8f9fa 0%, #eef4f1 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 16px 20px;
    }

    .aluno-card-title {
        font-size: 20px;
        font-weight: 600;
        color: #2f3b2f;
    }

    .aluno-card-title i {
        color: rgb(0,175,123);
        font-size: 24px;
        vertical-align: middle;
    }

    .aluno-card-body.modo-edicao {
        background-color: rgb(237,236,228);
    }

    .aluno-topo {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        padding: 18px;
        background: #fff;
        border: 1px solid #e6e6e6;
        border-radius: 10px;
    }

    .aluno-topo-foto {
        width: 120px;
    }

    .imagem_aluno {
        width: 100px;
        height: 120px;
        margin: 0 auto;
        border-radius: 10px;
        border: 1px solid #dcdcdc;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }

    .btn-camera-overlay {
        position: absolute;
        right: 6px;
        bottom: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background: rgba(0,0,0,.55);
        color: #fff !important;
        border-radius: 50%;
        text-decoration: none;
    }

    .aluno-upload-progress {
        margin-top: 12px;
    }

    .aluno-topo-info {
        flex: 1;
        min-width: 280px;
    }

    .aluno-meta .badge {
        font-size: 12px;
        padding: 7px 10px;
        margin-bottom: 6px;
    }

    .aluno-tabs {
        border-bottom: none;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        margin-bottom: 0;
        padding-bottom: 4px;
    }

    .aluno-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        color: #4f5b52;
        font-weight: 500;
        padding: 12px 16px;
        margin-right: 6px;
        background: #eef2ef;
    }

    .aluno-tabs .nav-link.active {
        background: rgb(0,175,123);
        color: #fff;
    }

    .aluno-tab-content {
        background: #fff;
        border: 1px solid #e4e7ea;
        border-radius: 0 10px 10px 10px;
        padding: 24px;
    }

    .section-card {
        background: #fcfcfc;
        border: 1px solid #e7e7e7;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #334;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 1px solid #ececec;
    }

    .field-box {
        margin-bottom: 18px;
    }

    .field-box label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #5b5b5b;
        margin-bottom: 6px;
    }

    .field-box .field-icon {
        margin-right: 6px;
        color: #6c757d;
    }

    .field-box .form-control,
    .field-box .custom-select,
    .field-box .no-style {
        border-radius: 8px;
        min-height: 40px;
    }

    .field-box .no-style {
        border: 1px solid #ced4da;
        background: #fff;
        padding: .375rem .75rem;
    }

    .aluno-form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .aluno-topo {
            display: block;
        }

        .aluno-topo-foto {
            width: 100%;
            margin-bottom: 15px;
        }

        .aluno-topo-info {
            min-width: auto;
        }
    }
</style>

{{-- resources/views/aluno/form.blade.php --}}

@php
    $isEdit = isset($aluno);

    $imgAluno = '/storage/imagens_aluno/no-image.jpg';
    if ($isEdit && isset($aluno->imagem_aluno) && !empty($aluno->imagem_aluno->nome_arquivo) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) {
        $imgAluno = '/storage/imagens_aluno/' . $aluno->ano_formacao->formacao . '/' . $aluno->imagem_aluno->nome_arquivo;
    }
@endphp

<div class="card shadow-sm border-0 aluno-card">
    <div class="card-header aluno-card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="aluno-card-title">
                <i class="ion-person-add mr-2"></i>
                {{ $isEdit ? 'Editar Aluno' : 'Implantar Aluno' }}
            </div>

            <div class="aluno-card-search">
                @include('aluno.pesquisaAluno')
            </div>
        </div>
    </div>

    <div class="card-body aluno-card-body {{ $isEdit ? 'modo-edicao' : '' }}">
        <div class="alert alert-success success-implantar-aluno" role="alert" style="display:none;"></div>
        <div class="alert alert-danger errors-implantar-aluno erro-upload" role="alert" style="display:none;"></div>

        <form id="implantar_aluno" enctype="multipart/form-data">
            {{ csrf_field() }}

            <input type="file" id="aluno_imagem" name="aluno_imagem" style="display:none;">

            <div class="aluno-topo">
                <div class="aluno-topo-foto">
                    <div id="aluno_img" class="imagem_aluno" style="background-image: url('{{ $imgAluno }}');">
                        <a class="no-style open_file btn-camera-overlay" href="javascript:void(0);">
                            <i class="ion-ios-camera"></i>
                        </a>
                    </div>

                    <div class="progress aluno-upload-progress" style="display:none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                             role="progressbar"
                             aria-valuenow="100"
                             aria-valuemin="0"
                             aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="aluno-topo-info">
                    <h4 class="mb-1">
                        {{ $isEdit ? ('AL ' . optional($aluno)->numero . ' ' . optional($aluno)->nome_guerra) : 'Novo cadastro de aluno' }}
                    </h4>

                    <p class="mb-1 text-muted">
                        {{ $isEdit ? optional($aluno)->nome_completo : 'Preencha os dados abaixo para cadastro ou edição.' }}
                    </p>

                    @if($isEdit)
                        <div class="aluno-meta">
                            <span class="badge badge-light mr-2">
                                <i class="ion-email mr-1"></i> {{ optional($aluno)->email }}
                            </span>

                            @if(optional($aluno->area)->area)
                                <span class="badge badge-light mr-2">
                                    <i class="ion-pinpoint mr-1"></i> {{ optional($aluno->area)->area }}
                                </span>
                            @endif

                            @if(optional($aluno->omct)->omct)
                                <span class="badge badge-light mr-2">
                                    <i class="ion-home mr-1"></i> {{ optional($aluno->omct)->omct }}
                                </span>
                            @endif

                            @if(!empty(optional($aluno->qms)->qms))
                                <span class="badge badge-success">
                                    {{ strtoupper(optional($aluno->qms)->qms) }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <ul class="nav nav-tabs aluno-tabs" id="nav-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#nav-implantar-aluno1">Matrícula no CFGS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno2">Primeiro Ano CFGS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno3">Situação Militar/Civil Anterior</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno4">Endereço Residencial Atual</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno5">Documentação</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno6">Dados Pessoais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno7">Dependentes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno8">Situação Sócio-Econômica</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno10">Fardamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#nav-implantar-aluno11">Observações</a>
                </li>
            </ul>

            <div class="tab-content aluno-tab-content" id="nav-tabContent">

                <div class="tab-pane fade show active" id="nav-implantar-aluno1" role="tabpanel">
                    @include('aluno.partials.tab_matricula')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno2" role="tabpanel">
                    @include('aluno.partials.tab_primeiro_ano')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno3" role="tabpanel">
                    @include('aluno.partials.tab_situacao_anterior')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno4" role="tabpanel">
                    @include('aluno.partials.tab_endereco')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno5" role="tabpanel">
                    @include('aluno.partials.tab_documentacao')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno6" role="tabpanel">
                    @include('aluno.partials.tab_dados_pessoais')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno7" role="tabpanel">
                    @include('aluno.partials.tab_dependentes')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno8" role="tabpanel">
                    @include('aluno.partials.tab_socioeconomica')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno10" role="tabpanel">
                    @include('aluno.partials.tab_fardamento')
                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno11" role="tabpanel">
                    @include('aluno.partials.tab_observacoes')
                </div>
            </div>

            <div class="aluno-form-footer">
                <button type="reset" class="btn btn-outline-secondary">
                    Limpar
                </button>

                <button type="button" class="btn btn-success">
                    {{ $isEdit ? 'Editar' : 'Salvar' }}
                </button>
            </div>
        </form>
    </div>
</div>