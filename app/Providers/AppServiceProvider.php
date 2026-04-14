<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema; // <--- ADICIONE ESTA LINHA

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define o comprimento padrão para campos string (importante para MySQL antigo)
        Schema::defaultStringLength(191);

        // Validação customizada para Identidade Militar EB
        Validator::extend('regidt_eb', function ($attribute, $value, $parameters, $validator) {
            $digits = preg_replace('/\D/', '', (string) $value);

            if (!preg_match('/^\d{10}$/', $digits)) {
                return false;
            }

            $base = substr($digits, 0, 9);
            $dvInformado = (int) substr($digits, 9, 1);
            $dvCalculado = self::calcularDvRegIdtEb($base);

            return $dvInformado === $dvCalculado;
        });

        Validator::replacer('regidt_eb', function ($message, $attribute, $rule, $parameters) {
            $label = trans("validation.attributes.{$attribute}", [], 'pt-BR');
            return "O {$label} é inválido (dígito verificador não confere).";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Cálculo do Dígito Verificador (DV) da Identidade Militar
     */
    private static function calcularDvRegIdtEb(string $base9): int
    {
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $n = (int) $base9[$i];
            if (($i % 2) === 0) {
                $n *= 2;
            }
            $soma += intdiv($n, 10) + ($n % 10);
        }
        $dv = (10 - ($soma % 10)) % 10;
        return $dv;
    }
}