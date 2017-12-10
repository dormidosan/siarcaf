<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    //
	protected $table = 'modulos';

	public function roles()
    {
        return $this->belongsToMany('App\Rol','modulo_rol')->withTimestamps();
    }

    public function padre()
    {
        return $this->belongsTo('App\Modulo','modulo_padre');
    }


}
