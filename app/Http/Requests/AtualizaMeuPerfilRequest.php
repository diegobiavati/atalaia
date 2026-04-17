<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizaMeuPerfilRequest extends FormRequest
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
            'tel_pronto_atendimento' => 'celular_com_ddd',
            'postograd_id' => 'numeric|min:1|max:25',
        ];
    }
    public function messages()
    {
        return [
            'celular_com_ddd' => 'Número de celular inválido!',
            'required' => 'Campo :attribute é inválido',
            'max' => 'Campo :attribute é inválido',
            'min' => 'Campo :attribute é inválido',
        ];
    }
}
