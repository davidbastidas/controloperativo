<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    public function usuario() {
      return $this->belongsTo('App\Usuarios', 'lector_id', 'id');
    }

    public function anomalia() {
      return $this->belongsTo('App\Anomalias', 'anomalia_id', 'id');
    }

    public function estado() {
      return $this->belongsTo('App\Estados', 'estado', 'id');
    }
}
