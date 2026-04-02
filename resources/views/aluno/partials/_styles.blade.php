<style>

    .readonly-mode input,
    .readonly-mode select,
    .readonly-mode textarea {
        background: #f5f5f5 !important;
        pointer-events: none;
    }
    
    .aluno-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
    }

    .card.bg-light.mb-3 {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
    }

    .card.bg-light.mb-3>.card-header {
        background: linear-gradient(90deg, #f8f9fa 0%, #edf5f1 100%);
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
    }

    .new-nav-tabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        padding-bottom: 6px;
        margin-bottom: 12px;
        border-bottom: 0;
    }

    .new-nav-item.new-nav-link {
        display: inline-block;
        margin-right: 6px;
        padding: 10px 14px;
        border-radius: 8px 8px 0 0;
        background: #eef2ef;
        text-decoration: none;
        flex: 0 0 auto;
    }

    .new-nav-item.new-nav-link.active {
        background: rgb(0, 175, 123);
    }

    .new-nav-item.new-nav-link.active font {
        color: #fff !important;
    }

    .card.bg-light.mb-3 .tab-content {
        background: #fff;
        border: 1px solid #e1e5e8;
        border-radius: 0 10px 10px 10px;
        overflow-x: hidden;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane {
        box-sizing: border-box;
        width: 100%;
        overflow: hidden;
        padding: 14px;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane.active,
    .card.bg-light.mb-3 .tab-content>.tab-pane.show.active {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        align-content: flex-start;
        gap: 14px;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno {
        box-sizing: border-box;
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 8px;
        padding: 12px;
        margin: 0 !important;
        flex: 1 1 220px;
        min-width: 220px;
        max-width: 100%;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno .no-style,
    .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno .custom-select,
    .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno .form-control {
        display: block;
        width: 100% !important;
        max-width: 100%;
        border-radius: 8px !important;
        box-sizing: border-box;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane .form-group,
    .card.bg-light.mb-3 .tab-content>.tab-pane .dependente,
    .card.bg-light.mb-3 .tab-content>.tab-pane .dependente-box,
    .card.bg-light.mb-3 .tab-content>.tab-pane #toClone,
    .card.bg-light.mb-3 .tab-content>.tab-pane #dependente-template,
    .card.bg-light.mb-3 .tab-content>.tab-pane>div[style*="width: 100%"] {
        box-sizing: border-box;
        width: 100% !important;
        max-width: 100%;
    }

    .card.bg-light.mb-3 .tab-content>.tab-pane .clear {
        flex-basis: 100%;
        width: 100%;
        height: 0;
        margin: 0 !important;
        padding: 0 !important;
        clear: both;
    }

    .labelDescricao {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #5b5b5b;
    }

    .no-style,
    .custom-select,
    .form-control {
        border-radius: 8px !important;
    }

    #nav-implantar-aluno1>.divImplantarAluno:first-child {
        flex: 0 0 110px;
        min-width: 110px;
        max-width: 110px;
    }

    /*#nav-implantar-aluno1 .imagem_aluno {
        width: 80px;
        height: 120px;
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        background-color: #f7f7f7;
    }

    #nav-implantar-aluno1 .imagem_aluno .open_file {
        position: absolute;
        right: 4px;
        bottom: 4px;
        width: 24px;
        height: 24px;
        line-height: 24px;
        text-align: center;
        background: rgba(255, 255, 255, .9);
        border-radius: 50%;
    }*/

    #nav-implantar-aluno7 {
        align-items: stretch;
    }

    #nav-implantar-aluno7>div:last-child {
        margin-top: 8px !important;
    }

    @media (max-width: 1200px) {
        .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno {
            min-width: 180px;
        }
    }

    @media (max-width: 992px) {
        .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno {
            min-width: calc(50% - 7px);
            width: calc(50% - 7px) !important;
        }

        #nav-implantar-aluno1>.divImplantarAluno:first-child {
            min-width: 100%;
            max-width: 100%;
            flex-basis: 100%;
        }
    }

    @media (max-width: 768px) {

        .card.bg-light.mb-3 .tab-content>.tab-pane .divImplantarAluno,
        .card.bg-light.mb-3 .tab-content>.tab-pane .form-group {
            width: 100% !important;
            min-width: 100%;
            max-width: 100%;
            flex-basis: 100%;
        }

        .aluno-form-actions {
            flex-wrap: wrap;
        }
    }

    .imagem_aluno {
        width: 80px;
        height: 120px;
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        background-color: #f7f7f7;
    }

    .imagem_aluno .open_file {
        position: absolute;
        right: 5px;
        bottom: 6px;
        width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        background: rgba(255,255,255,.92);
        border-radius: 50%;
        z-index: 20;
        cursor: pointer;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }

    .imagem_aluno .open_file i {
        font-size: 16px;
        color: #696969;
    }
</style>