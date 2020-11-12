<?php

/* https://github.com/LaravelLegends/pt-br-validator */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperadoresRequest extends FormRequest
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
            'nome' => 'required|max:100|min:6',
            'nome_guerra' => 'required|max:30|min:2',
            'tel_pronto_atendimento' => 'celular_com_ddd',
            'postograd_id'=>'required|numeric|min:1|max:25',
            'omcts_id'=>'numeric|min:1|max:20',
            'email'=>'required|email',
            'idt_militar' => 'required',
            'idt_militar_o_exp' => 'required'
        ];
    }
    
    public function messages()
    {
        return [
            'celular_com_ddd' => 'Número de celular inválido!',
            'required' => 'Campo :attribute é inválido', 
            'max' => 'Campo :attribute é inválido', 
            'min' => 'Campo :attribute é inválido',
            'email' => 'Email informado é inválido',
        ];
    }
}
