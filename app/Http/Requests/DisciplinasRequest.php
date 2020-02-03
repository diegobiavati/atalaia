<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisciplinasRequest extends FormRequest
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
            'nome_disciplina' => 'required|max:100|min:6',
            'nome_disciplina_abrev' => 'required|max:30|min:2',
            //'peso' => 'numeric',
        ];
    }
    public function messages()
    {
        return [
            'required' => 'Campo :attribute é inválido', 
            'max' => 'Campo :attribute é muito curto.', 
            'min' => 'Campo :attribute é muito curto.',
            'numeric' => 'Campo :attribute deve ser numérico.',
        ];
    }    
}
