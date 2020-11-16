<?php

namespace App\Models\Maxpoint;

use App\Models\AppModel;

class UsersPos extends AppModel
{
    protected $connection = 'sqlsrv_maxpoint';
    protected $table = 'Users_Pos';
    protected $primaryKey = 'IDUsersPos';
    protected $keyType = 'string';
    public $incrementing = false;


}
