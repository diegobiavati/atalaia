<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Contato;

class BemVindo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $usuario_nome;

    public function __construct($usuario_nome)
    {
        $this->usuario_nome = $usuario_nome;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $contato = Contato::find(1);
        return $this->from('atalaia@esa.eb.mil.br', 'Sistema Atalaia')
                    ->subject('Bem-vindo')
                    ->view('emails.bem-vindo')
                    ->with('nome_user', $this->usuario_nome)
                    ->with('contato', $contato);
    }
}
