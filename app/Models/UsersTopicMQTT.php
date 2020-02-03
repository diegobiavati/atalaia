<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersTopicMQTT extends Model
{
    protected $table = 'mqtt_topic_users';
    public $timestamps = false;


    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
