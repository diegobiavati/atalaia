<?php

namespace App\Rules;

use App\Models\EsaAvaliacoes;
use Illuminate\Contracts\Validation\Rule;

class ESANomeAvaliacoes implements Rule
{
    private $_nomes = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $esaAvaliacoes = new EsaAvaliacoes();
        $this->_nomes = $esaAvaliacoes->getTodasAvaliacoes()->keyBy('id')->keys()->all();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, $this->_nomes);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute não é válido.';
    }
}
