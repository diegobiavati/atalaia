<?php

Route::group(['middleware' => 'auth', 'prefix' => 'operador', 'as' => 'operador.'], function () {
    Route::get('/', ['as' => 'admin', 'uses' => 'Operador\AdminOpController@ShowHome']);
});

Route::group(['middleware' => 'auth', 'prefix' => 'aluno', 'as' => 'aluno.'], function () {
    Route::get('/', ['as' => 'painel', 'uses' => 'Aluno\PainelAlunoController@ShowHome']);
});

Route::group(['prefix' => 'exportar/modo-impressao', 'as' => 'exportar-modo-impressao.'], function () {
    Route::get('portaria/{id}', ['as' => 'portaria', 'uses' => 'Exportar\ExportarImpressaoController@ImprimirPortaria']);
});

Route::group(['prefix' => 'download', 'as' => 'download.'], function () {
    Route::get('mostra/{id}/{hash}', ['as' => 'mostra', 'uses' => 'Ajax\AjaxAvaliacoesController@downloadArquivoMostra']);
    Route::get('reposta-mostra/{id}/{hash}', ['as' => 'reposta-mostra', 'uses' => 'Ajax\AjaxAvaliacoesController@downloadArquivoRespostaMostra']);
});

Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {

    /* ROTAS PARA SUPER ADMINISTRADORES E OPERADORES (MENU PRINCIPAL)*/
    Route::get('gerenciar-operadores', ['as' => 'gerenciar-operadores', 'uses' => 'Ajax\AjaxAdminController@GerenciarOperadores']);
    
    Route::get('anos-de-formacao', ['as' => 'anos-de-formacao', 'uses' => 'Ajax\AjaxAdminController@AnosDeFormacao']);
    Route::get('gerenciar-disciplinas', ['as' => 'gerenciar-disciplinas', 'uses' => 'Ajax\AjaxAdminController@GerenciarDisciplinas']);
    Route::get('avaliacoes', ['as' => 'avaliacoes', 'uses' => 'Ajax\AjaxAdminController@Avaliacoes']);
    Route::get('conselho-escolar', ['as' => 'conselho-escolar', 'uses' => 'Ajax\AjaxAdminController@ConselhoEscolar']);
    Route::get('lancar-taf-aluno', ['as' => 'lancar-taf-aluno', 'uses' => 'Ajax\AjaxAdminController@LancarTafAluno']);
    Route::post('lancar-abdominal-aluno', ['as' => 'lancar-abdominal-aluno', 'uses' => 'Ajax\AjaxAdminController@LancarAbdominalAluno']);
    Route::post('editar-abdominal-aluno', ['as' => 'editar-abdominal-aluno', 'uses' => 'Ajax\AjaxAdminController@EditarAbdominalAluno']);

    Route::post('lancar-tfm-aluno', ['as' => 'lancar-tfm-aluno', 'uses' => 'Ajax\AjaxAdminController@LancarTfmAluno']);
    Route::post('editar-tfm-aluno', ['as' => 'editar-tfm-aluno', 'uses' => 'Ajax\AjaxAdminController@EditarTfmAluno']);

    Route::get('lancar-taf-aluno-recuperacao', ['as' => 'lancar-taf-aluno-recuperacao', 'uses' => 'Ajax\AjaxAdminController@LancarTafAluno']);
    Route::get('menu-tfm-aluno', ['as' => 'menu-tfm-aluno', 'uses' => 'Ajax\AjaxOperadorController@MenuTfmAluno']);
    Route::post('gravar-taf-aluno', ['as' => 'gravar-taf-aluno', 'uses' => 'Ajax\AjaxAdminController@GravarTafAluno']);
    Route::post('gravar-taf-recuperacao-aluno', ['as' => 'gravar-taf-recuperacao-aluno', 'uses' => 'Ajax\AjaxAdminController@GravarTafRecuperacaoAluno']);
    Route::post('atualizar-taf-aluno', ['as' => 'atualizar-taf-aluno', 'uses' => 'Ajax\AjaxAdminController@AtualizarTafAluno']);
    Route::get('alunos', ['as' => 'alunos', 'uses' => 'Ajax\AjaxAdminController@GerenciarAlunos']);
    Route::get('viewLancamentos', ['as' => 'lancamentos', 'uses' => 'Ajax\AjaxAdminController@ViewLancamentos']);
    Route::get('voluntarios-para-aviacao', ['as' => 'voluntarios-para-aviacao', 'uses' => 'Ajax\AjaxAdminController@VoluntariosParaAviacao']);
    Route::get('escolha-de-qms', ['as' => 'escolha-de-qms', 'uses' => 'Ajax\AjaxAdminController@EscolhaDeQms']);
    Route::get('precedencia-desempate', ['as' => 'precedencia-desempate', 'uses' => 'Ajax\AjaxAdminController@PrecedenciaDesempate']);
    Route::get('visao-geral', ['as' => 'visao-geral', 'uses' => 'Ajax\AjaxAdminController@VisaoGeral']);
    
    Route::get('relatorios', ['as' => 'relatorios', 'uses' => 'Ajax\AjaxAdminController@Relatorios']);

    //Atualização Ten João Victor
    Route::post('remover-pronto-faltas/{id}', 'Ajax\AjaxAdminController@RemoverProntoFaltas');
    Route::resource('importacoes', 'Ajax\ImportacaoController');
    Route::resource('parametros', 'Ajax\ParametrosController');
    Route::post('parametrosInfo', 'Ajax\ParametrosController@loadInfo');
    Route::post('parametrosUpdateInfo', 'Ajax\ParametrosController@updateInfo');
    Route::get('parametrosDeleteInfo/{id}', 'Ajax\ParametrosController@deleteInfo');
    Route::resource('lancamentos', 'Ajax\LancamentosController');
    Route::post('lancamentosTurma', 'Ajax\LancamentosController@ViewTurma');
    Route::post('consultaTurma', 'Ajax\LancamentosController@ConsultaTurma');
    Route::post('listaFatosObservados', 'Ajax\LancamentosController@ViewListaFatosObservados');
    Route::post('listaFATD', 'Ajax\LancamentosController@ViewListaFATD');
    Route::get('fatd/{id}', 'Ajax\LancamentosController@ViewTelaFATD');
    Route::get('ficha-fatd', ['as' => 'ficha-fatd', 'uses' => 'Ajax\LancamentosController@ViewFichaFATD']);
    Route::post('fatdSargenteante/{id}', 'Ajax\LancamentosController@LancarFatdSargenteante');    
    Route::get('view-revisao-prova', 'Ajax\AjaxAvaliacoesController@ViewRevisaoProva');
    Route::post('aprova-revisao-prova-uete/{id}', 'Ajax\AjaxAvaliacoesController@AprovaRevisaoUete');

    Route::get('view-frad-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewFradAluno');
    Route::get('consulta-frad-aluno', 'Relatorios\RelatorioAlunoController@ViewRelacaoFradAlunos');

    Route::get('view-rod-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRodAluno');
    Route::get('consulta-rod-aluno', 'Relatorios\RelatorioAlunoController@ViewRelacaoRodAlunos');
    Route::post('rod-conteudo-atitudinal', 'Ajax\ParametrosController@store');

    Route::get('view-ficha-disciplinar/{id_ano_formacao}', 'Ajax\AjaxAdminController@ViewSelecaoUeteAluno');
    Route::post('view-ficha-disciplinar/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelacaoFDisciplinarAlunos');

    Route::get('view-relacao-punidos/{id_ano_formacao}', 'Ajax\AjaxAdminController@ViewSelecaoUeteAlunoPunicao');
    Route::post('view-relacao-punidos/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelacaoAlunoUetePunido');

    Route::get('view-relacao-reprovados/{id_ano_formacao}', 'Ajax\AjaxAdminController@ViewAlunosReprovados');
    Route::post('view-relacao-reprovados/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelacaoAlunoReprovado');
    

    /* ROTA PARA O CHAT COM ALUNO VIA TELEGRAM */

    Route::get('fale-com-aluno', ['as' => 'fale-com-aluno', 'uses' => 'Ajax\AjaxChatAlunoController@FaleComAluno']);
    Route::post('carregar-mensagens', ['as' => 'carregar-mensagens', 'uses' => 'Ajax\AjaxChatAlunoController@CarregarMensagens']);
    Route::post('enviar-mensagem-aluno', ['as' => 'enviar-mensagem-aluno', 'uses' => 'Ajax\AjaxChatAlunoController@EnviarMensagemAluno']);
    Route::post('enviar-mensagem-especial-aluno', ['as' => 'enviar-mensagem-especial-aluno', 'uses' => 'Ajax\AjaxChatAlunoController@EnviarMensagemEspecialAluno']);
    Route::get('dialog-mensagens-especiais', ['as' => 'dialog-mensagens-especiais', 'uses' => 'Ajax\AjaxChatAlunoController@DialogMensagensEspeciais']);

    /* ROTAS PARA OPERADORES */

    Route::get('visao-geral-omct', 'Ajax\AjaxOperadorController@VisaoGeralOMCT');
    Route::get('dialog-pronto-de-faltas/{id}', 'Ajax\AjaxOperadorController@DialogProntoFaltas');
    Route::get('dialog-lancar-graus/{id}', 'Ajax\AjaxOperadorController@DialogLancarGraus');
    Route::get('registrar-grau-aluno/{id}/{avaliacaoID}/{gbo}', 'Ajax\AjaxOperadorController@RegistrarGrauAluno');
    Route::get('editar-registro-grau-aluno/{id}/{avaliacaoID}', 'Ajax\AjaxOperadorController@EditarRegistroGrauAluno');

    Route::post('enviar-pronto-faltas/{id}', 'Ajax\AjaxOperadorController@EnviarProntoFaltas');

    /* ROTAS DE AÇÕES PARA AjaxAdminController */

    Route::get('dialog-editar-operador/{id}', 'Ajax\AjaxAdminController@DialogEditarOperador');
    
    Route::get('dialog-editar-disciplina/{id}', 'Ajax\AjaxAdminController@DialogEditarDisciplina');
    Route::get('dialog-editar-avaliacao/{id}', 'Ajax\AjaxAdminController@DialogEditarAvaliacao');
    Route::get('dialog-editar-ano-formacao/{id}', 'Ajax\AjaxAdminController@DialogEditarAnoFormacao');
    Route::get('dialog-adicionar-operador/', 'Ajax\AjaxAdminController@DialogAdicionarOperador');
    
    Route::get('dialog-adicionar-ano-formacao/', 'Ajax\AjaxAdminController@DialogAdicionarAnoFormacao');
    Route::get('dialog-adicionar-disciplina/', 'Ajax\AjaxAdminController@DialogAdicionarDisciplina');
    Route::get('dialog-adicionar-portaria/', 'Ajax\AjaxAdminController@DialogAdicionarPortaria');
    Route::get('dialog-importar-disciplina/', 'Ajax\AjaxAdminController@DialogImportarDisciplina');
    Route::get('dialog-adicionar-avaliacao/', 'Ajax\AjaxAdminController@DialogAdicionarAvaliacao');
    Route::get('dialog-adicionar-avaliacao-recuperacao/', 'Ajax\AjaxAdminController@DialogAdicionarAvaliacaoRec');
    Route::get('dialog-chamadas/{id}/{avaliacao?}', 'Ajax\AjaxAdminController@DialogChamadas');
    Route::get('dialog-editar-nome-portaria/{id}', 'Ajax\AjaxAdminController@DialogEditarNomePortaria');
    Route::get('dialog-adicionar-periodo-escolha-qms/', 'Ajax\AjaxAdminController@DialogAdicionarPeriodoEscolhaQMS');
    Route::get('dialog-editar-periodo-escolha-qms/{id}', 'Ajax\AjaxAdminController@DialogEditarPeriodoEscolhaQMS');
    Route::get('dialog-editar-meu-perfil/', 'Ajax\AjaxAdminController@DialogEditarMeuPerfil');
    Route::get('dialog-info-user/{tipo}/{id}', 'Ajax\AjaxAdminController@DialogInfoUser');

    //Original Julião... 
    //Route::get('dialog-implantar-aluno/', 'Ajax\AjaxAdminController@DialogImplantarAluno');   
    //Route::get('dialog-editar-cadastro-aluno/{pesquisa}/{tipo}', 'Ajax\AjaxAdminController@DialogEditarCadastroAluno');    

    //Route::get('dialog-implantar-aluno/', 'Ajax\AjaxImplantarAlunoController@DialogImplantarAluno');    
    //Criado Por Ten João Victor
    Route::resource('admin/aluno', 'Aluno\AlunoApiController');
    //Route::resource('visao-geral', 'Aluno\AlunoApiController');  

    //Original Julião...
    //Route::get('dialog-adicionar-aluno-situacao-diversa/{id}', 'Ajax\AjaxAdminController@DialogAdicionarAluSitDiv');
    //Criado Por Ten João Victor
    Route::resource('admin/alunoSitDiversas', 'Aluno\AlunoSitDiversasController');

    Route::get('dialog-editar-cadastro-aluno-situacoes-diversas/{id}', 'Ajax\AjaxAdminController@DialogEditarCadastroAlunoSitDiv');
    Route::get('dialog-periodo-lancamento-taf/', 'Ajax\AjaxAdminController@DialogPeriodoLancamentoTAF');
    Route::get('relacao-voluntarios-aviacao', 'Ajax\AjaxAdminController@DialogRelacaoVoluntariosAviacao');
    Route::get('relacao-alunos-aviacao', 'Ajax\AjaxOperadorController@ListagemAlunosUete')->middleware('checkauth');
    Route::get('relacao-selecao-exame', 'Ajax\AjaxOperadorController@ListagemAlunosExames')->middleware('checkauth');
    Route::get('dialog-conselho-escolar/{aluno}', 'Ajax\AjaxAdminController@DialogConselhoEscolar');
    Route::get('carrega-nota-aluno-disciplina/{aluno}/{disciplina}', 'Ajax\AjaxAdminController@CarregaNotaAlunoDisciplina');
    Route::get('adicionar-concessao-conselho/{aluno}/{disciplina}/{acrescimo}', 'Ajax\AjaxAdminController@AdicionarConcessaoConselho');
    Route::get('remover-concessao-conselho/{aluno}/{disciplina}/', 'Ajax\AjaxAdminController@RemoverConcessaoConselho');

    Route::get('remover-operador/{id}', 'Ajax\AjaxAdminController@RemoverOperador');
    Route::get('remover-aluno/{id}', 'Ajax\AjaxAdminController@RemoverAluno');
    Route::get('remover-ano-formacao/{id}', 'Ajax\AjaxAdminController@RemoverAnoFormacao');
    Route::get('remover-disciplina/{id}', 'Ajax\AjaxAdminController@RemoverDisciplina');
    Route::get('remover-avaliacao/{id}', 'Ajax\AjaxAdminController@RemoverAvaliacao');
    Route::get('remover-indice-corrida/{id}', 'Ajax\AjaxAdminController@RemoverIndiceCorrida');
    Route::get('remover-indice-flexbra/{id}', 'Ajax\AjaxAdminController@RemoverIndiceFlexBra');
    Route::get('remover-indice-flexbar/{id}', 'Ajax\AjaxAdminController@RemoverIndiceFlexBar');
    Route::get('remover-indice-abdomin/{id}', 'Ajax\AjaxAdminController@RemoverIndiceAbdomin');
    Route::get('remover-bonus-atletas/{id}', 'Ajax\AjaxAdminController@RemoverBonusAtletas');
    Route::get('remover-periodo-escolha-qms/{id}', 'Ajax\AjaxAdminController@RemoverEscolhaQMS');
    Route::get('remover-portaria/{id}', 'Ajax\AjaxAdminController@RemoverPortaria');
    Route::get('remover-img-perfil/{id}', 'Ajax\AjaxAdminController@RemoverImagemPerfil');
    Route::get('clonar-portaria/{id}', 'Ajax\AjaxAdminController@ClonarPortaria');
    Route::get('load-content-portaria/{id}', 'Ajax\AjaxAdminController@LoadContentPortaria');
    Route::get('load-alunos-situacoes-diversas/', 'Ajax\AjaxAdminController@LoadAlunosSitDiv');
    Route::get('marcar-suficiencia/{modo}/{id}', 'Ajax\AjaxAdminController@MarcarSuficiencia');
    Route::get('marcar-exercicio-avaliado/{modo}/{id}', 'Ajax\AjaxAdminController@MarcarSuficiencia');
    Route::get('marcar-universo/{modo}/{val}/{id}', 'Ajax\AjaxAdminController@MarcarUniverso');
    Route::get('marcar-voluntario-aviacao/', 'Ajax\AjaxAdminController@MarcarVoluntarioAviacao');
    Route::get('opcoes-listagem-selecao-alunos/', 'Ajax\AjaxAdminController@OpcoesdeListagemSelecaoAlunos');
    Route::get('alterar-precedencia/{aluno_id}/{precedencia}', 'Ajax\AjaxAdminController@AlterarPrecedencia');

    Route::post('upload/img-perfil/{tipo}/{id}', 'Ajax\AjaxUploadsController@UploadImgPerfil');

    //Atualização João
    Route::post('upload/img-aluno/{tipo}/{id}', 'Ajax\AjaxUploadsController@UploadImgAluno');
    Route::post('upload/arquivo-mostra/{arquivo}', 'Ajax\AjaxAvaliacoesController@uploadArquivoMostra');
    Route::post('upload/resposta-arquivo-mostra/{arquivo}', 'Ajax\AjaxAvaliacoesController@uploadRepostaArquivoMostra');

    Route::post('upload/importCSV', 'Ajax\AjaxUploadsController@ImportCSV');
    Route::post('atualizar-operador/{id}', 'Ajax\AjaxAdminController@AtualizarOperador');
    Route::post('atualizar-ano-formacao/{id}', 'Ajax\AjaxAdminController@AtualizarAnoFormacao');
    Route::post('atualizar-periodo-escolha-qms/{id}', 'Ajax\AjaxAdminController@AtualizarPeriodoEscolhaQMS');
    Route::post('adicionar-operador/', 'Ajax\AjaxAdminController@AdicionarOperador');
    Route::post('adicionar-ano-formacao/', 'Ajax\AjaxAdminController@AdicionarAnoFormacao');
    Route::post('atualizar-disciplina/{id}', 'Ajax\AjaxAdminController@AtualizarDisciplina');
    Route::post('adicionar-disciplina/', 'Ajax\AjaxAdminController@AdicionarDisciplina');
    Route::post('editar-periodo-lanca-taf/', 'Ajax\AjaxAdminController@AtualizarPeriodoLancamentoTAF');
    Route::post('adicionar-avaliacao/', 'Ajax\AjaxAdminController@AdicionarAvaliacao');
    Route::post('editar-avaliacao/{id}', 'Ajax\AjaxAdminController@EditarAvaliacao');
    Route::post('adicionar-avaliacao-recuperacao/', 'Ajax\AjaxAdminController@AdicionarAvaliacaoRecuperacao');
    Route::post('adicionar-portaria/', 'Ajax\AjaxAdminController@AdicionarPortaria');
    Route::post('adicionar-periodo-escolha-qms/', 'Ajax\AjaxAdminController@AdicionarPeriodoEscolhaQMS');
    Route::post('importar-disciplinas/', 'Ajax\AjaxAdminController@ImportarDisciplinas');
    Route::post('incluir-indice-corrida/', 'Ajax\AjaxAdminController@incluirIndiceCorrida');
    Route::post('incluir-indice-flexbra/', 'Ajax\AjaxAdminController@incluirIndiceFlexBra');
    Route::post('incluir-indice-flexbar/', 'Ajax\AjaxAdminController@incluirIndiceFlexBar');
    Route::post('incluir-indice-abdomin/', 'Ajax\AjaxAdminController@incluirIndiceAbdomin');
    Route::post('incluir-bonus-atletas/', 'Ajax\AjaxAdminController@incluirBonusAtletas');
    Route::post('renomear-portaria/{id}', 'Ajax\AjaxAdminController@RenomearPortaria');
    Route::post('atualizar-meu-perfil/{id}', 'Ajax\AjaxAdminController@AtualizarMeuPerfil');

    //Original Julião...
    //Route::post('implantar-aluno/', 'Ajax\AjaxAdminController@ImplantarAluno');
    Route::post('implantar-aluno/', 'Ajax\AjaxImplantarAlunoController@RegistrarAluno');
    Route::post('atualizar-cadastro-aluno/{id}', 'Ajax\AjaxAdminController@AtualizarCadastroAluno');
    Route::post('incluir_aluno_situacao_diversa/{id}', 'Ajax\AjaxAdminController@AdicionarAluSitDiv');
    Route::post('atualizar_aluno_situacao_diversa/{id}', 'Ajax\AjaxAdminController@AtualizarAluSitDiv');
    Route::post('relacao-aptos-aviacao/', 'Ajax\AjaxAdminController@AtualizarRelacaoAptosAviacao');
    Route::post('selecao-voluntarios-aviacao/', 'Ajax\AjaxOperadorController@SelecaoVoluntariosAviacao')->middleware('checkauth');
    Route::post('selecao-voluntarios-exame-aviacao/', 'Ajax\AjaxOperadorController@SelecaoVoluntariosExameAviacao')->middleware('checkauth');

    /* ROTAS DE AÇÕES PARA AjaxRelatoriosController */

    Route::get('carrega-opcoes-relatorio/{item}', 'Ajax\AjaxRelatoriosController@OpcoesRelatoriosDefault');
    Route::get('pronto-de-faltas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ProntoDeFaltas');
    Route::get('pronto-lancamento-notas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ProntoLancamentoNotas');
    Route::get('pronto-lancamento-notas-ar/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ProntoLancamentoNotasAR');
    Route::get('demonstrativo-notas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@DemonstrativoNotas');
    Route::get('demonstrativo-notas-por-aluno/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@DemonstrativoNotasPorAluno');
    Route::get('recibo-demonstrativo/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ReciboDemonstrativo');
    Route::get('recibo-demonstrativo-por-aluno/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ReciboDemonstrativoPorAluno');
    Route::get('alunos-em-recuperacao/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AlunosRecuperacao');
    Route::get('resultado-avaliacao-por-nota/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ResultadoAvaliacaoPorNota');
    Route::get('select-alunos-fizeram-avaliacao/{id}', 'Ajax\AjaxRelatoriosController@SelectAlunosFizeramAvaliacao');
    Route::get('classificacao-final-aluno/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ClassificacaoFinalAluno');
    Route::get('pronto-lancamento-taf/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ProntoLancamentoTAF');
    Route::get('alunos-conselho-escolar/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AlunosConselhoEscolar');

    Route::get('alunos-sem-escolha-qms/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AlunosSemEscolhaQMS');
    Route::get('comprovante-escolha-qms/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@ComprovanteEscolhaQMS');
    Route::get('relatorios-escolha-qms/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@RelatoriosEscolhaQMS');
    Route::get('alunos-em-recuperacao-por-disciplinas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AlunosEmRecPorDisciplina');
    Route::get('dados-estatisticos-de-avaliacoes/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@DadosEstatisticosDeAvaliacoes');
    Route::get('analise-parcial-provas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AnaliseParcialProvas');
    Route::get('analise-parcial-disciplinas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AnaliseParcialDisciplinas');
    Route::get('analise-parcial-pb/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AnaliseParcialNPB');
    Route::get('resultado-final-pb/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@resultadoFinalPB');

    //original Julião
    //Route::get('relacao-geral-alunos/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@RelacaoGeralAlunos');
    Route::get('relacao-geral-alunos/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelatorioGeral');
    Route::get('mapa-efetivo-geral/{id_ano_formacao}', 'Relatorios\MapaEfetivoController@ViewMapaEfetivoGeral');
    Route::get('mapa-efetivo-desligado/{id_ano_formacao}', 'Relatorios\MapaEfetivoController@ViewMapaEfetivoDesligado');
    Route::get('mapa-evasao-escolar/{id_ano_formacao}', 'Relatorios\MapaEfetivoController@ViewMapaEvasaoEscolar');

    Route::get('ficha-individual-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewFichaIndividualAluno');
    Route::get('relacao-alunos', 'Relatorios\RelatorioAlunoController@ViewRelacaoAlunos');

    Route::get('dados-estatistico-gerais/{id_ano_formacao}', 'Relatorios\DadosEstatisticosGeraisController@ViewDadosEstatisticosGerais');

    Route::get('alunos-situacoes-diversas/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@RelacaoAlunosSituacaoDiversas');
    Route::get('alunos-sit-div-hist-escolar/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@SituacaoDiversasHistoricoEscolar');
    Route::get('relacao-atletas-marexaer/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@RelacaoAtletasMarexaer');
    Route::get('relatorio-voluntarios-aviacao/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@RelatorioVoluntariosAviacao');
    Route::get('alunos-sem-cadastro-telegram/{id_ano_formacao}', 'Ajax\AjaxRelatoriosController@AlunosSemCadastroTelegram');

    Route::get('dialog-configuracoes-relatorios', 'Ajax\AjaxRelatoriosController@DialogConfiguracoesRelatorio');
    Route::post('configurar-relatorios', 'Ajax\AjaxRelatoriosController@ConfigurarRelatorio');

    /* ASSISTENTE INSTALAÇÃO APP */

    Route::get('assistente-instalacao-app/{id}', 'Ajax\AssistenteInstalacaoAppController@LoadCarouselAssistente');
    Route::get('escolha-de-qms-aluno/{alunoID}/{userID}', 'Ajax\AjaxEscolhaDeQMSAlunoController@DialogEscolhadeQMSAluno');
    Route::post('gravar-opcoes-aluno', 'Ajax\AjaxEscolhaDeQMSAlunoController@GravarOpcoesAluno');
    Route::get('limpar-opcoes-aluno/{escolha_qms_id}/{aluno_id}', 'Ajax\AjaxEscolhaDeQMSAlunoController@LimparOpcoesAluno');
});

/* ROTAS PARA SUBMIT DE RELATÓRIOS */

Route::group(['prefix' => 'relatorios', 'as' => 'relatorios.'], function () {
    Route::get('pronto-de-faltas', ['as' => 'pronto_de_faltas', 'uses' => 'Relatorios\RelatoriosController@ProntoDeFaltas']);
    Route::get('pronto-lancamento-notas', ['as' => 'pronto_lancamento_notas', 'uses' => 'Relatorios\RelatoriosController@ProntoLancamentoNotas']);
    Route::get('pronto-lancamento-notas-ar', ['as' => 'pronto_lancamento_notas_ar', 'uses' => 'Relatorios\RelatoriosController@ProntoLancamentoNotasAR']);
    Route::get('pronto-lancamento-taf', ['as' => 'pronto_lancamento_taf', 'uses' => 'Relatorios\RelatoriosController@ProntoLancamentoTAF']);
    Route::get('demonstrativo-notas', ['as' => 'demonstrativo_notas', 'uses' => 'Relatorios\RelatoriosController@DemonstrativoNotas']);
    Route::get('lista-assinavel-do-demonstrativo-notas', ['as' => 'lista_assinavel_demosntrativo_notas', 'uses' => 'Relatorios\RelatoriosController@ListaAssinavelDemonstrativoNotas']);
    Route::get('lista-assinavel-do-demonstrativo-notas-por-aluno', ['as' => 'lista_assinavel_demosntrativo_notas_por_aluno', 'uses' => 'Relatorios\RelatoriosController@ListaAssinavelDemonstrativoNotasPorAluno']);

    //Original Julião
    //Route::get('relacao-alunos-prontos', ['as' => 'relacao_alunos_prontos', 'uses' => 'Relatorios\RelatoriosController@RelacaoAlunosProntos']);
    Route::get('relacao-alunos-prontos', ['as' => 'relacao_alunos_prontos', 'uses' => 'Relatorios\RelatorioAlunoController@RelacaoAlunosProntos']);
    Route::get('relacao-mapa-efetivo-geral', ['as' => 'relacao-mapa-efetivo-geral', 'uses' => 'Relatorios\MapaEfetivoController@RelacaoMapaEfetivoGeral']);
    Route::get('relacao-mapa-efetivo-desligado', ['as' => 'relacao-mapa-efetivo-desligado', 'uses' => 'Relatorios\MapaEfetivoController@RelacaoMapaEfetivoDesligado']);
    Route::get('relacao-evasao-escolar', ['as' => 'relacao-evasao-escolar', 'uses' => 'Relatorios\MapaEfetivoController@RelacaoMapaEvasaoEscolar']);
    Route::get('relacao-ficha-individual-aluno', ['as' => 'relacao-ficha-individual-aluno', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioRelacaoAlunos']);
    Route::get('relacao-frad-aluno', ['as' => 'relacao-frad-aluno', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFRADAlunos']);
    Route::get('relacao-frad-geral', ['as' => 'relacao-frad-geral', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFRADAlunos']);

    Route::get('relacao-rod-aluno', ['as' => 'relacao-rod-aluno', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioRODAlunos']);
    Route::get('relacao-rod-geral', ['as' => 'relacao-rod-geral', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioRODAlunos']);

    Route::get('ficha-disciplinar-aluno', ['as' => 'ficha-disciplinar-aluno', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFichaDisciplinarAlunos']);
    Route::get('ficha-disciplinar-geral', ['as' => 'ficha-disciplinar-geral', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFichaDisciplinarAlunos']);

    Route::get('ficha-aluno-punido', ['as' => 'ficha-aluno-punido', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFichaAlunoPunidos']);
    Route::get('ficha-aluno-punido-geral', ['as' => 'ficha-aluno-punido-geral', 'uses' => 'Relatorios\RelatorioAlunoController@RelatorioFichaAlunoPunidos']);

    Route::get('relacao-dados-estatisticos-geral', ['as' => 'relacao-dados-estatisticos-geral', 'uses' => 'Relatorios\DadosEstatisticosGeraisController@RelacaoEstatistica']);

    Route::get('relacao-alunos-situacoes-diversas', ['as' => 'relacao_alunos_situacoes_diversas', 'uses' => 'Relatorios\RelatoriosController@RelacaoAlunosSituacoesDiversas']);
    Route::get('relacao-voluntarios-qms-aviacao', ['as' => 'relacao_voluntarios_qms_aviacao', 'uses' => 'Relatorios\RelatoriosController@RelacaoVoluntariosQMSAviacao']);
    Route::get('alunos-sem-cadastro-telegram', ['as' => 'alunos_sem_cadastro_telegram', 'uses' => 'Relatorios\RelatoriosController@RelacaoAlunosSemCadastroTelegram']);
    Route::get('resultado-avaliacao-por-nota', ['as' => 'resultado_avaliacao_por_nota', 'uses' => 'Relatorios\RelatoriosController@ResultadoAvaliacaoPorNota']);
    Route::get('relacao-atletas-marexaer', ['as' => 'relacao_atletas_marexaer', 'uses' => 'Relatorios\RelatoriosController@RelacaoAtletasMarexaer']);
    Route::get('classificacao-geral', ['as' => 'classificacao_geral', 'uses' => 'Relatorios\RelatoriosController@ClassificacaoGeral']);
    Route::get('alunos-em-recuperacao', ['as' => 'alunos_em_recuperacao', 'uses' => 'Relatorios\RelatoriosController@AlunosRecuperacao']);
    Route::get('lista-alunos-conselho', ['as' => 'lista_alunos_conselho', 'uses' => 'Relatorios\RelatoriosController@AlunosConselhoEscolar']);
    Route::get('alunos-nao-escolheram-qms', ['as' => 'alunos_nao_escolheram_qms', 'uses' => 'Relatorios\RelatoriosController@AlunosNaoEscolheramQMS']);
    Route::get('comprovante-escolha-qms', ['as' => 'comprovante_escolha_qms', 'uses' => 'Relatorios\RelatoriosController@ComprovanteEscolhaQMS']);
    Route::get('relatorios_escolha_qms', ['as' => 'relatorios_escolha_qms', 'uses' => 'Relatorios\RelatoriosController@RelatoriosEscolhaQMS']);
    Route::get('alunos-em-recuperacao-em-disciplinas', ['as' => 'alunos_em_recuperacao_em_disciplinas', 'uses' => 'Relatorios\RelatoriosController@AlunosEmRecPorDisciplina']);
    Route::get('dados-estatisticos-de-avaliacoes', ['as' => 'dados_estatisticos_de_avaliacoes', 'uses' => 'Relatorios\RelatoriosController@DadosEstatisticosDeAvaliacoes']);
    Route::get('visualizar-historico-aluno/', ['as' => 'visualizar_historico_aluno', 'uses' => 'Relatorios\RelatoriosController@LoadHistoricoAluno']);

    Route::get('relacao_final_periodo_basico', ['as' => 'relacao_final_periodo_basico', 'uses' => 'Relatorios\RelatoriosController@relacaoFinalPeriodoBasico']);
    Route::get('analise-parcial-provas/', ['as' => 'analise_parcial_provas', 'uses' => 'Relatorios\AnalisesNotasController@AnaliseParcialProvas']);
    Route::get('analise-parcial-disciplinas/', ['as' => 'analise_parcial_disciplinas', 'uses' => 'Relatorios\AnalisesNotasController@AnaliseParcialDisciplinas']);
    Route::get('analise-parcial-npb/', ['as' => 'analise_parcial_npb', 'uses' => 'Relatorios\AnalisesNotasController@AnaliseParcialNPB']);

    Route::get('graficos/', ['as' => 'graficos', 'uses' => 'Relatorios\AnalisesNotasController@AnaliseDeResultados']);

    Route::post('ajax/aplicar-escolha-qms', ['as' => 'aplicar-escolha-qms', 'uses' => 'Ajax\AjaxAdminController@AplicaEscolhaQms']);
    Route::post('ajax/aplicar-escolha-qms-bi', ['as' => 'aplicar-escolha-qms-bi', 'uses' => 'Ajax\AjaxAdminController@AplicaEscolhaQmsBI']);

    /* Diploma Digital */
    Route::get('ajax/disciplinas-diploma-uete/{ano_formacao_id}', ['as' => 'disciplinas-diploma-uete', 'uses' => 'Relatorios\RelatoriosController@RelatorioDisciplinasDiplomaUete']);
});

/* AUTENTICAÇÃO ROTAS */

Route::get('/home', function () {
    return redirect()->route('atalaia');
});

Route::get('/', ['as' => 'login', 'uses' => 'OwnAuthController@UserLogin']);


Route::get('notadisc', ['as' => 'notadisc', 'uses' => 'Ajax\AjaxAdminController@notaDisc']);
Route::get('atalaia', ['as' => 'atalaia', 'uses' => 'OwnAuthController@UserRouter']);
Route::post('auth', ['as' => 'auth', 'uses' => 'OwnAuthController@AuthUser']);
Route::post('password/email', ['as' => 'send_recovery_password', 'uses' => 'OwnAuthController@SendRecoveryPassword']);
Route::get('password/reset/{token}/{email}', ['as' => 'dialog_recovery_password', 'uses' => 'OwnAuthController@DialogRecoveryPassword']);
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('submit_new_password');

Route::get('/sair', function () {
    auth()->logout();
    session()->flush();
    return redirect()->route('login');
})->name('atalaia.logout');

/* FIM DAS ROTAS DE AUTENTICAÇÃO */

/* MQTT */

Route::get('register-user-mqtt', ['as' => 'register_user_mqtt', 'uses' => 'MQTTController@RegisterUserMQTT']);
Route::get('notify-offline-user/{id}', 'MQTTController@NotifyOfflineUser');

//Lembrar de passar os parâmetros
//Route::get('eslog/{ano_formacao_id}/{escolhaQMS}', 'EslogController@retornaAlunos');

Route::get('pdfTeste', function(){
    Fpdf::AddPage();
    Fpdf::SetFont('Courier', 'B', 18);
    Fpdf::Cell(50, 25, 'Hello World!');
    Fpdf::Output();
    exit();
});

//Reintegrar Aluno Situacao Diversas
//Route::get('reintegrar/{requisicao}/{sistema}/{idaluno}', 'Aluno\AlunoSitDiversasController@update');

//Gerar Notas Aluno Capitani
Route::get('modelo-pb-capitani/{id_ano_formacao}', 'Aluno\AlunoApiController@modeloPBCapitani')->middleware('checkauth');

//Importador de Arquivo Excel para integrar o banco de dados do SisPB com o Atalaia
Route::resource('importar-excel-sispb-alunos', 'Utilitarios\ImportadorController');


/************************************************************************************************************
*                              INICIO DE ROTAS REFERENTE AO SISTEMA GAVIÃO                                  *
*                                                                                                           *
*                                                                                                           * 
*                                                                                                           *
**************************************************************************************************************/

/* AUTENTICAÇÃO ROTAS GAVIÃO */
Route::get('/gaviao', ['as' => 'gaviao', 'uses' => 'OwnAuthController@UserLogin']);
Route::get('/gaviaoRouter', ['as' => 'gaviaoRouter', 'uses' => 'OwnAuthController@UserRouter']);

Route::get('gaviao/dashboard', ['as' => 'gaviao.dashboard', 'uses' => 'Operador\AdminOpController@DashboardGaviao'])->middleware('checkauth');

Route::post('auth_gaviao', ['as' => 'auth_gaviao', 'uses' => 'OwnAuthController@AuthGaviaoUser']);

Route::get('/gaviao/sair', function () {
    auth()->logout();
    session()->flush();
    return redirect()->route('gaviao');
})->name('gaviao.logout');
/* FIM AUTENTICAÇÃO DE ROTAS GAVIÃO */

Route::group(['prefix' => 'gaviao/ajax', 'as' => 'gaviao.ajax.'], function () {

    Route::get('show-checkbox-anoformacao-qms/{id}', 'Ajax\AjaxAdminGaviaoController@ShowChkBoxAnoFormacaoQms')->middleware('checkauth');
    Route::get('anos-de-formacao', ['as' => 'anos-de-formacao', 'uses' => 'Ajax\AjaxAdminController@AnosDeFormacao']);
    Route::get('visao-geral-gaviao', ['as' => 'visao-geral-gaviao', 'uses' => 'Ajax\AjaxAdminGaviaoController@VisaoGeralGaviao']);
    Route::get('gerenciar-operadores-gaviao', ['as' => 'gerenciar-operadores-gaviao', 'uses' => 'Ajax\AjaxAdminGaviaoController@GerenciarOperadoresGaviao'])->middleware('checkauth');
    Route::get('dialog-editar-operador-gaviao/{id}', 'Ajax\AjaxAdminGaviaoController@DialogEditarOperadorGaviao')->middleware('checkauth');
    Route::get('dialog-adicionar-operador-gaviao', 'Ajax\AjaxAdminGaviaoController@DialogAdicionarOperadorGaviao')->middleware('checkauth');
    Route::get('alunos-gaviao', ['as' => 'alunos-gaviao', 'uses' => 'Ajax\AjaxAdminGaviaoController@GerenciarAlunosGaviao'])->middleware('checkauth');

    Route::get('seletorQms/{qms_id}', ['as' => 'seletorQms', 'uses' => 'Ajax\AjaxAdminGaviaoController@SelecionaQMS'])->middleware('checkauth');

    Route::resource('admin/aluno', 'Aluno\AlunoApiController')->middleware('checkauth');

    Route::get('listagem-selecao-alunos-gaviao', 'Ajax\AjaxAdminGaviaoController@ListagemSelecaoAlunosGaviao')->middleware('checkauth');
    Route::get('relacao-geral-alunos/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelatorioGeralGaviao')->middleware('checkauth');
    Route::get('listagem-selecao-alunos-turma', 'Ajax\AjaxAdminGaviaoController@ListagemSelecaoAlunosTurma')->middleware('checkauth');
    Route::post('seleciona-turma/{idTurma}/{idAluno}', 'Ajax\AjaxAdminGaviaoController@SelecionaAlunoTurma')->middleware('checkauth');

    Route::get('load-alunos-gaviao-situacoes-diversas/', 'Ajax\AjaxAdminGaviaoController@LoadAlunosSitDiv');

    Route::get('relacao-alunos', 'Relatorios\RelatorioAlunoController@ViewRelacaoAlunos')->middleware('checkauth');
    Route::get('ficha-individual-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewFichaIndividualAlunoGaviao')->middleware('checkauth');

    Route::get('view-frad-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewFradAlunoGaviao')->middleware('checkauth');
    Route::get('consulta-frad-aluno', 'Relatorios\RelatorioAlunoController@ViewRelacaoFradAlunos')->middleware('checkauth');

    Route::get('view-rod-aluno/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRodAlunoGaviao')->middleware('checkauth');
    Route::get('consulta-rod-aluno', 'Relatorios\RelatorioAlunoController@ViewRelacaoRodAlunos')->middleware('checkauth');

    Route::get('view-ficha-disciplinar/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewFichaDisciplinarGaviao')->middleware('checkauth');
    Route::post('view-relacao-ficha-disciplinar/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelacaoFDisciplinarAlunos')->middleware('checkauth');

    Route::get('view-audiencia-fo/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewAudienciaFO')->middleware('checkauth');
    Route::post('view-relacao-audiencia-fo/', 'Relatorios\RelatorioAlunoController@ViewRelacaoAudienciaFO')->middleware('checkauth');

    Route::get('view-relacao-punidos/{id_ano_formacao}', 'Ajax\AjaxAdminController@ViewSelecaoUeteAlunoPunicao')->middleware('checkauth');
    Route::post('view-relacao-punidos/{id_ano_formacao}', 'Relatorios\RelatorioAlunoController@ViewRelacaoAlunoUetePunido')->middleware('checkauth');
    
    //Lançamentos FATD, FO
    Route::get('viewLancamentos', ['as' => 'lancamentos', 'uses' => 'Ajax\AjaxAdminController@ViewLancamentos'])->middleware('checkauth');
    Route::get('carregaSelectCurso/{qmsID}', 'Ajax\AjaxRelatoriosGaviaoController@CarregaSelectCiaCurso')->middleware('checkauth');

    Route::get('carrega-opcoes-relatorio/{item}', 'Ajax\AjaxRelatoriosGaviaoController@OpcoesRelatoriosDefault');

    /* ROTAS PARA SUBMIT DE RELATÓRIOS */
    Route::get('view-relatorios', ['as' => 'view-relatorios', 'uses' => 'Ajax\AjaxAdminGaviaoController@Relatorios']);

    Route::group(['prefix' => 'relatorios', 'as' => 'relatorios.'], function () {
        Route::get('download-pdf/{arquivo}', 'Relatorios\RelatorioAlunoController@Download')->middleware('checkauth');
    });

    /* Rotas Para Diploma Digital */
    Route::get('view-diploma', ['as' => 'view-diploma', 'uses' => 'Ajax\AjaxAdminGaviaoController@DiplomaDigital'])->middleware('checkauth');

    Route::group(['prefix' => 'diploma', 'as' => 'diploma.'], function () {
        Route::resource('diploma-periodo', 'Ajax\Diploma\DiplomaController')->middleware('checkauth');


        //Exportar o Xlsx para o Diploma Digital
        Route::get('exportaAluno', ['as' => 'exportaAluno', 'uses' => 'Ajax\Diploma\DiplomaController@exportAlunos'])->middleware('checkauth');
    });

    //Route::get('correcao-comandanteCurso', 'Ajax\LancamentosController@CorrecaoComandante');
});