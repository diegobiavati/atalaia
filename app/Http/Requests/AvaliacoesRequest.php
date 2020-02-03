<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvaliacoesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome_completo' => 'required|max:100|min:3',
            'nome_abrev' => 'required|max:30|min:2',
            'gbm'=> 'numeric|min:1|',
            //'peso' => 'numeric|min:1',
            'prazo_nota' => 'numeric|min:1',
            'disciplinas_id'=> 'numeric',
            'observacao' => 'min:0|max:10000',
        ];
    }
    public function messages()
    {
        return [
            'required' => 'Campo :attribute é inválido', 
            'max' => 'Campo :attribute é inválido e excede o tamanho permitido', 
            'min' => 'Campo :attribute é inválido e é menor que o tamanho permitido',
            'numeric' => 'Campo :attribute deve ser numérico',
        ];
    }
}
