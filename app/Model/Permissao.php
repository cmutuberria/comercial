<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = 'permissao_sistema';    
    public $timestamps = false;

    /**
     * Usuario.
     */
    public function Usuario()
    {
        return $this->belongsTo('App\Model\Usuario','co_usuario');
    }

    

}

