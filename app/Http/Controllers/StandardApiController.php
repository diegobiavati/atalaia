<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\Request;

class StandardApiController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    
    private $path = false;
    private $totalPaginate = 15;


    public function index()
    {
        $data = $this->model->all();

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $this->validate($request, $this->model->rules());

        $dataForm = $request->all();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $extension = $request->image->extension();

            $name = uniqid(date('d-m-Y-H:i:s-'));

            $nameFile = "{$name}.{$extension}";

            $upload = Image::make($dataForm['image'])->resize(200, 200)->save(storage_path("app/public/{$this->path}/" . $nameFile, 100));

            if (!$upload) {
                return response()->json(['error' => 'Falha ao fazer upload'], 500);

            } else {
                $dataForm['image'] = $nameFile;
            }

        }

        $data = $this->model->create($dataForm);

        return response()->json($data, 201);
    }

    public function show($id)
    {
        if (!$data = $this->model->find($id)) {
            return response()->json(['error' => 'Ops... Isso non eczisteeee!'], 404);
        } else {
            return response()->json($data);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$data = $this->model->find($id))
            return response()->json(['error' => $this->name . ' Não encontrada'], 404);
        
         //Valida os dados
        $this->validate($request, $this->model->rules($id));

        $dataForm = $request->all();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            if ($data->image) {
                if (Storage::exists("{$this->path}/{$data->image}"))
                    Storage::delete("{$this->path}/{$data->image}");
            }
            $extension = $request->image->extension();

            $name = uniqid(date('d-m-Y-H:i:s-'));

            $nameFile = "{$name}.{$extension}";

            $pasta = $this->path;            
    
            /* Quando for no linux, descomenta a linha de baixo! */
            $upload = Image::make($dataForm['image'])->resize(200, 200)->save(storage_path("app/public/{$this->path}/" . $nameFile, 100));

            if (!$upload) {
                return response()->json(['error' => 'Falha ao fazer upload'], 500);

            } else {
                $dataForm['image'] = $nameFile;
            }

        }

        $data->update($dataForm);
        return response()->json($data);

    }

    public function destroy($id)
    {
        if (!$data = $this->model->find($id))
            return response()->json(['error' => 'Ops... Isso non eczisteeee!'], 404);

        if ($data->image) {
            if (Storage::exists("{$this->path}/{$data->image}"))
                Storage::delete("{$this->path}/{$data->image}");
        }

        $data->delete();
        return response()->json(['success' => 'Deletado com sucesso']);

    }
}