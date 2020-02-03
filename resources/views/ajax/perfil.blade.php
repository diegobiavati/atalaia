    <style>
    p.card-text h5{
        font-size: 6px;
    }
    </style>
    <div class="card bg-light mb-3">
        <div class="card-header"><i class="ion-happy"></i><strong>Perfil do usuário</strong></div>
            <div class="card-body">
              <!--h5 class="card-title"></h5-->
              <p class="card-text">
                    <div style="width: 60%; margin: 12px auto; border-bottom: 1px solid #ccc;">
                        <h6>Imagem do perfil</h6>
                    </div>
                    <div style="width: 40px; margin: 0 auto; padding: 18px 4px;">
                        <div class="imagem_perfil" style="background: url({{$img_perfil}}) no-repeat center center; background-size: cover;">
                            <div>
                                <a class="no-style open_input_file" href="javascript: void(0);">
                                    <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                                </a>
                            </div>
                        </div>                        
                        <form id="img_perfil" method="post" enctype="multipart/form-data"-->
                            @csrf
                            <input type="file" name="imagem" onchange="submitForm(\'img_perfil\');" style="display: none;" />
                        </form>
                        
                    </div>
                    <div style="width: 60%; margin: 12px auto; border-bottom: 1px solid #ccc;">
                        <h6>Posto ou graduação</h6>
                    </div>
                    <div style="width: 60%; margin: 12px auto; border-bottom: 1px solid #ccc;">
                        <h6>Telefone de pronto atendimento</h6>
                    </div>
                    <div style="width: 60%; margin: 12px auto; border-bottom: 1px solid #ccc;">
                        <h6>Redefinir senha</h6>
                    </div>
              </p>
            </div>
    </div>  