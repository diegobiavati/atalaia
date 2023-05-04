<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificaBoletim extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
	public $_militar;

    public function __construct($militar)
    {
        $this->_militar = $militar;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        return $this->from('atalaia@esa.eb.mil.br', 'Sistema Atalaia')
                    ->subject('Bem-vindo')
                    ->view('emails.verifica-boletim')
					->with('militar', $this->_militar);
    }
}
