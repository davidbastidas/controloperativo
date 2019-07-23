<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $table = 'usuarios';
    public function tipo() {
      return $this->belongsTo('App\TipoUsuario', 'tipo_id', 'id');
    }
}
