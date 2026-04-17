<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Militar extends Model
{
    protected $connection = 'mysql_sistemas_esa';
    protected $table = 'esanet_boletins_indexados';
}
