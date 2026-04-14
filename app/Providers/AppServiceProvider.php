<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('regidt_eb', function ($attribute, $value, $parameters, $validator) {
            // Aceita com ou sem hífen/pontos (ex: 040998630-4 ou 0409986304)
            $digits = preg_replace('/\D/', '', (string) $value);

            // Tem que ter 10 dígitos
            if (!preg_match('/^\d{10}$/', $digits)) {
                return false;
            }

            $base = substr($digits, 0, 9);      // 9 primeiros
            $dvInformado = (int) substr($digits, 9, 1); // último

            $dvCalculado = self::calcularDvRegIdtEb($base);

            return $dvInformado === $dvCalculado;
        });

        Validator::replacer('regidt_eb', function ($message, $attribute, $rule, $parameters) {

            $label = trans("validation.attributes.{$attribute}", [], 'pt-BR');
            
            return "O {$label} é inválido (dígito verificador não confere).";
        });
        
        Schema::defaultStringLength(191);
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

     private static function calcularDvRegIdtEb(string $base9): int
    {
        // Regra (descrita na norma): multiplica por 2 os algarismos em posição ímpar (1ª, 3ª, 5ª, 7ª, 9ª),
        // repete os pares, soma os dígitos do resultado e subtrai da próxima dezena. :contentReference[oaicite:1]{index=1}
        $soma = 0;

        for ($i = 0; $i < 9; $i++) {
            $n = (int) $base9[$i];

            // $i começa em 0, então posições ímpares "humanas" = índices pares (0,2,4,6,8)
            if (($i % 2) === 0) {
                $n *= 2;
            }

            // soma dígitos do produto (ex: 18 => 1+8)
            $soma += intdiv($n, 10) + ($n % 10);
        }

        // próxima dezena - soma (se já for dezena exata, DV = 0)
        $dv = (10 - ($soma % 10)) % 10;

        return $dv;
    }
}
