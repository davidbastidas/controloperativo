<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\Pci;
use App\Anomalias;
use App\ObservacionesRapidas;
use App\Usuarios;
use App\Log;
use App\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ApiController extends Controller
{
  public function login(Request $request)
  {
    $response = null;
    $usuarios = Usuarios::where('nickname', '=', $request->user)->where('contrasena', '=', $request->password)->first();
    if(isset($usuarios->id)){
      $response = array(
        'estado' => true,
        'nombre' => $usuarios->nombre,
        'nickname' => $usuarios->nickname,
        'tipo' => $usuarios->tipo_id,
        'fk_delegacion' => $usuarios->delegacion_id,
        'fk_id' => $usuarios->id
      );
    } else {
      $response = array(
        'estado' => false
      );
    }

    return $response;
  }
  public function getServicios(Request $request)
  {
    $arrayAuditorias = [];
    $arrayAnomalias = [];
    $arrayPci = [];
    $arrayObservacionesRapidas = [];
    $arrayFINAL = [];
    $collection = null;

    $fechaHoy = Carbon::now()->format('Y-m-d');
    $auditorias = Auditoria::select('agenda_id')
                    ->where('lector_id', '=', $request->user)
                    ->where('estado', '=', '1')
                    ->groupBy('agenda_id')->get();
    $agendas = Agenda::where('fecha', '<=', "'$fechaHoy'");
    foreach ($auditorias as $auditoria) {
      $agendas = $agendas->orWhere('id', $auditoria->agenda_id);
    }
    $agendas = $agendas->get();

    $arrayIn = array();
    foreach ($agendas as $agenda) {
      $arrayIn[] = $agenda->id;
    }

    $auditorias = Auditoria::where('lector_id', '=', $request->user)
                    ->where('estado', '=', '1')
                    ->whereIn('agenda_id', $arrayIn)->get();
    foreach ($auditorias as $auditoria) {
      array_push($arrayAuditorias, (object) array(
        'id' => $auditoria->id,
        'barrio' => $auditoria->barrio,
        'localidad' => $auditoria->localidad,
        'cliente' => $auditoria->cliente,
        'direccion' => $auditoria->direccion,
        'nic' => $auditoria->nic,
        'ruta' => $auditoria->ruta,
        'itin' => $auditoria->itin,
        'medidor' => $auditoria->medidor,
        'motivo' => $auditoria->motivo,
        'nis' => $auditoria->nis,
        'pide_gps' => $auditoria->pide_gps
      ));
    }

    //buscando Pci
    $pcis = Pci::select('agenda_id')
                    ->where('lector_id', '=', $request->user)
                    ->where('estado', '=', '1')
                    ->groupBy('agenda_id')->get();
    $agendas = Agenda::where('fecha', '<=', "'$fechaHoy'");
    foreach ($pcis as $pci) {
      $agendas = $agendas->orWhere('id', $pci->agenda_id);
    }
    $agendas = $agendas->get();

    $arrayIn = array();
    foreach ($agendas as $agenda) {
      $arrayIn[] = $agenda->id;
    }

    $pcis = Pci::where('lector_id', '=', $request->user)
                    ->where('estado', '=', '1')
                    ->whereIn('agenda_id', $arrayIn)->get();
    foreach ($pcis as $pci) {
      array_push($arrayPci, (object) array(
        'id' => $pci->id,
        'ct' => $pci->ct,
        'mt' => $pci->mt,
        'direccion' => $pci->direccion,
        'medidor' => $pci->medidor,
        'medidor_anterior' => $pci->medidor_anterior,
        'medidor_posterior' => $pci->medidor_posterior,
        'barrio' => $pci->barrio,
        'municipio' => $pci->municipio,
        'codigo' => $pci->codigo,
        'an_anterior' => $pci->an_anterior,
        'lectura_anterior' => $pci->lectura_anterior,
        'unicom' => $pci->unicom,
        'ruta' => $pci->ruta,
        'itin' => $pci->itin,
        'pide_gps' => $pci->pide_gps
      ));
    }

    $anomalias = Anomalias::all();
    foreach ($anomalias as $anomalia) {
      array_push($arrayAnomalias, (object) array(
        'id' => $anomalia->id,
        'nombre' => $anomalia->nombre,
        'codigo' => $anomalia->codigo
        'lectura' => $anomalia->lectura
        'foto' => $anomalia->foto
      ));
    }

    $observaciones = ObservacionesRapidas::all();
    foreach ($observaciones as $observacion) {
      array_push($arrayObservacionesRapidas, (object) array(
        'id' => $observacion->id,
        'nombre' => $observacion->nombre
      ));
    }

    array_push($arrayFINAL, (object) array(
      'estado' => true,
      'anomalias' => $arrayAnomalias,
      'observaciones_rapidas' => $arrayObservacionesRapidas,
      'auditorias' => $arrayAuditorias,
      'pci' => $arrayPci
    ));
    $collection = new Collection($arrayFINAL);
    return $collection;
  }

  public function actualizarAuditoria(Request $request)
  {
    $response = null;
    if($request->user){
      $auditoria = Auditoria::where('id', '=', $request->id)->where('estado', '=', '1')->first();
      if(isset($auditoria->id)){
        if($request->anomalia == 0){
          $request->anomalia = null;
        }
        if($request->observacion_rapida == 0){
          $request->observacion_rapida = null;
        }
        $auditoria->anomalia_id = $request->anomalia;
        $auditoria->observacion_rapida = $request->observacion_rapida;
        $auditoria->lectura = $request->lectura;
        $auditoria->observacion_analisis = $request->observacion_analisis;
        $auditoria->latitud = $request->latitud;
        $auditoria->longitud = $request->longitud;
        $auditoria->fecha_recibido = $request->fecha_realizado;
        $auditoria->fecha_recibido_servidor = Carbon::now();
        $auditoria->estado = 2;
        $auditoria->orden_realizado = $request->orden_realizado;

        $auditoria->save();

        try {
          $logSeg = new Log();
          $logSeg->log = '' . $request;
          $logSeg->servicio_id = $auditoria->id;
          $logSeg->save();

          if($request->foto != null || $request->foto != ""){
            //decode base64 string
            $image = base64_decode($request->foto);

            $archivo = $auditoria->id . '.png';
            \File::put(config('myconfig.ruta_fotos_auditoria') . $archivo, $image);
          }
        } catch (\Exception $e) {
          $logSeg = new Log();
          $logSeg->log = '' . $e;
          $logSeg->servicio_id = $auditoria->id;
          $logSeg->save();
        } finally {

        }

        $response = array(
          'estado' => true
        );
      } else {
        $response = array(
          'estado' => true
        );
      }
    } else {
      $response = array(
        'estado' => false
      );
    }

    return $response;
  }

  public function actualizarPci(Request $request)
  {
    $response = null;
    if($request->user){
      $pci = Pci::where('id', '=', $request->id)->where('estado', '=', '1')->first();
      if(isset($pci->id)){
        if($request->anomalia == 0){
          $request->anomalia = null;
        }
        $pci->anomalia_id = $request->anomalia;
        $pci->lectura = $request->lectura;
        $pci->observacion_analisis = $request->observacion_analisis;
        $pci->latitud = $request->latitud;
        $pci->longitud = $request->longitud;
        $pci->fecha_recibido = $request->fecha_realizado;
        $pci->fecha_recibido_servidor = Carbon::now();
        $pci->estado = 2;
        $pci->orden_realizado = $request->orden_realizado;

        $pci->save();

        try {
          $logSeg = new Log();
          $logSeg->log = '' . $request;
          $logSeg->servicio_id = $pci->id;
          $logSeg->save();

          if($request->foto != null || $request->foto != ""){
            //decode base64 string
            $image = base64_decode($request->foto);

            $archivo = $pci->id . '.png';
            \File::put(config('myconfig.ruta_fotos_pci') . $archivo, $image);
          }
        } catch (\Exception $e) {
          $logSeg = new Log();
          $logSeg->log = '' . $e;
          $logSeg->servicio_id = $pci->id;
          $logSeg->save();
        } finally {

        }

        $response = array(
          'estado' => true
        );
      } else {
        $response = array(
          'estado' => true
        );
      }
    } else {
      $response = array(
        'estado' => false
      );
    }

    return $response;
  }
}
