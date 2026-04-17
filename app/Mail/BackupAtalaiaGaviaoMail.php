<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BackupAtalaiaGaviaoMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $filePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Backup Diário do Sistema')
                    ->view('emails.backup')
                    ->attach($this->filePath, [
                        'as' => basename($this->filePath),
                        'mime' => 'application/gzip',
                    ]);
    }
}
