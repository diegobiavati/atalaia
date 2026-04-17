<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramMsgEnviadas extends Model
{
    protected $table = 'telegram_msg_enviadas';
    public $timestamps = false;

    public function dataMsg()
    {
        if ($this->data_hora) {
            $data = strftime('%A, %d de %B às %H:%M', strtotime($this->data_hora));
        } else {
            $data = null;
        }

        return $data;
    }
}
