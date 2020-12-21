<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Alunos;
use App\Models\ImagemAluno;
use App\Models\Imagens;
use App\User;
use Illuminate\Support\Facades\Storage;

class AjaxUploadsController extends Controller
{

    protected $classLog;

    public function __construct(\App\Http\OwnClasses\ClassLog $classLog){
        $this->classLog = $classLog;
        $classLog->ip=(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: null);
    }

    public function UploadImgPerfil(Request $request){

        /*
        // tipos de imagens de perfil
        
        {tipo}
        operador
        minha-imagem
        aluno

        */
        if(!$request->hasFile('imagem')){
            $error[] = 'Não foi enviado nenhum arquivo';
        }

        if($request->imagem->getMimeType()!='image/jpeg' && $request->imagem->getMimeType()!='image/png') {
            $error[] = 'Por favor, utilize imagens com extensão jpeg ou png';
        }

        if(($request->imagem->getClientSize()/1024)>1024){
            $error[] = 'Por favor, utilize imagens de no máximo 1024Kb';    
        }

        if(!isset($error)){
            $fileName = uniqid().'.'.$request->imagem->extension();            
            if($request->imagem->storeAs('imagens_perfil', $fileName)){
                $imagem = new Imagens;
                $imagem->imagem = '/storage/imagens_perfil/'.$fileName;
                $imagem->save();

                $user = User::find($request->id);

                if(isset($user->imagens->imagem) && $user->imagens_id!=1){
                    unlink($_SERVER["DOCUMENT_ROOT"].$user->imagens->imagem);
                    Imagens::destroy($user->imagens_id);
                }

                $user->imagens_id = $imagem->id;
                $user->save();

            } else {
                $error[] = 'Houve um erro tentar gravar o arquivo.';    
            }
        }

        $data['uploadType'] = 'imgPefil';

        if(!isset($error)){
            $data['status'] = 'ok';
            $data['tipo'] = ($request->id==auth()->id())?'minha-imagem':$request->tipo;
            $data['src_image'] = $imagem->imagem;
        } else {
            $data['status'] = 'err';
            $data['error'] = implode('<br />', $error);
        }
        $this->classLog->RegistrarLog('Acesso ao menu de conselho de ensino', auth()->user()->email);
        return $data;

    }

    public function UploadImgAluno(Request $request){

        /*
        // tipos de imagens de perfil
        
        {tipo}
        operador
        minha-aluno_imagem
        aluno

        */
        if(!$request->hasFile('aluno_imagem')){
            $error[] = 'Não foi enviado nenhum arquivo';
        }

        if($request->aluno_imagem->getMimeType()!='image/jpeg' && $request->aluno_imagem->getMimeType()!='image/png') {
            $error[] = 'Por favor, utilize imagens com extensão jpeg ou png';
        }

        if(($request->aluno_imagem->getClientSize()/1024)>1024){
            $error[] = 'Por favor, utilize imagens de no máximo 1024Kb';    
        }

        if(!isset($error)){

            if($request->hasFile('aluno_imagem') && $request->file('aluno_imagem')->isValid()){

                $extension = $request->aluno_imagem->extension();
                $name = uniqid(date('His'));
                $nameFile = "{$name}.{$extension}";

                $aluno = Alunos::with('ano_formacao')->with('imagem_aluno')->find($request->id);
                
                if(isset($aluno->imagem_aluno->id)){
                    Storage::disk('public')->delete('/imagens_aluno/'.$aluno->ano_formacao->formacao.'/'.$aluno->imagem_aluno->nome_arquivo);
                    
                    $imagemAluno = ImagemAluno::find($aluno->imagem_aluno->id);
                }else{
                    $imagemAluno = new ImagemAluno;
                    $imagemAluno->id_aluno = $request->id;
                }
                
                if($request->aluno_imagem->storeAs('imagens_aluno/'.$aluno->ano_formacao->formacao, $nameFile)){
                   
                   $imagemAluno->nome_arquivo = $nameFile;
                   
                   if(isset($imagemAluno->id)){
                        $imagemAluno->update();
                   }else{
                        $imagemAluno->save();//Salva o Arquivo...
                   }
                }
            }

        }

        $data['uploadType'] = 'imgAluno';

        if(!isset($error)){
            $data['status'] = 'ok';
            $data['response'] = 'Foto Incluída com Sucesso!!!';
            //$data['tipo'] = ($request->id==auth()->id())?'minha-aluno_imagem':$request->tipo;
            //$data['src_image'] = $aluno_imagem->aluno_imagem;
        } else {
            $data['status'] = 'err';
            $data['error'] = implode('<br />', $error);
        }
        $this->classLog->RegistrarLog('Upload de Imagem do Aluno', auth()->user()->email);
        return $data;

    }

    public function ImportCSV(Request $request){
        if($request->hasFile('csv_file')){
            //$data = file();
            $data2 = nl2br(file_get_contents($request->file('csv_file')->getRealPath()));
            if($request->csv_file->getMimeType()=='text/plain' && $request->csv_file->getClientOriginalExtension()=='csv'){
                
            }

        } else {
            return 'Arquivo invalido!<br />';
        }

            $sql[] = 'Ok';

            return implode('<br />', $sql);

        
    }

}
