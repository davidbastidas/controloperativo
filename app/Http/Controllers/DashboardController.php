<?php

namespace App\Http\Controllers;

use App\Agenda;
use App\Auditoria;
use App\Pci;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function getAvancePorGestor(Request $request){
    $agendas = Agenda::where('fecha', 'LIKE', DB::raw("'%$request->fecha%'"));
    if($request->has('delegacion_filtro')){
      $delegacion_filtro = $request->delegacion_filtro;
      if($delegacion_filtro != 0){
        $agendas = $agendas->where('tipo_lectura_id', $delegacion_filtro);
      }
    }
    $agendas = $agendas->get();

    $arrayAgendas = [];
    $count = 0;
    $stringIn = '';
    foreach ($agendas as $agenda) {
      $arrayAgendas[] = $agenda->id;
      if($count == 0){
        $stringIn = $agenda->id;
        $count++;
      } else {
        $stringIn .= ',' . $agenda->id;
      }
    }

    $gestores = [];
    if(count($arrayAgendas) > 0){
      if($request->delegacion_filtro == 1){
        $gestores = Auditoria::select(
                DB::raw("a.lector_id"),
                DB::raw("u.nombre"),
                DB::raw("(select count(1) from auditoria ar where a.lector_id = ar.lector_id and ar.estado > 1 and ar.agenda_id in ($stringIn)) as realizados"),
                DB::raw("(select count(1) from auditoria ar where a.lector_id = ar.lector_id and ar.estado = 1 and ar.agenda_id in ($stringIn)) as pendientes")
            )
            ->from(DB::raw('auditoria a'))
            ->join('usuarios as u', 'u.id', '=', 'a.lector_id')
            ->whereIn('a.agenda_id', $arrayAgendas);
      }else if($request->delegacion_filtro == 2){
        $gestores = Pci::select(
                DB::raw("a.lector_id"),
                DB::raw("u.nombre"),
                DB::raw("(select count(1) from pci ar where a.lector_id = ar.lector_id and ar.estado > 1 and ar.agenda_id in ($stringIn)) as realizados"),
                DB::raw("(select count(1) from pci ar where a.lector_id = ar.lector_id and ar.estado = 1 and ar.agenda_id in ($stringIn)) as pendientes")
            )
            ->from(DB::raw('pci a'))
            ->join('usuarios as u', 'u.id', '=', 'a.lector_id')
            ->whereIn('a.agenda_id', $arrayAgendas);
      }

      if($request->has('gestor_filtro')){
        $gestor_filtro = $request->gestor_filtro;
        if($gestor_filtro != 0){
          $gestores = $gestores->where('a.lector_id', $gestor_filtro);
        }
      }
      if($request->has('estados_filtro')){
        $estados_filtro = $request->estados_filtro;
        if($estados_filtro != 0){
          if($estados_filtro == 2){
            $gestores = $gestores->where('a.estado', '>', 1);
          }else{
            $gestores = $gestores->where('a.estado', $estados_filtro);
          }
        }
      }
      $gestores = $gestores->groupBy('a.lector_id', 'u.nombre', 'realizados', 'pendientes')
                            ->orderBy('u.nombre')->get();
    }

    return response()->json([
        'gestores' => $gestores
    ]);
  }

  public function getAvanceDiario(Request $request){
    $agendas = Agenda::where('fecha', 'LIKE', DB::raw("'%$request->fecha%'"));
    if($request->has('delegacion_filtro')){
      $delegacion_filtro = $request->delegacion_filtro;
      if($delegacion_filtro != 0){
        $agendas = $agendas->where('tipo_lectura_id', $delegacion_filtro);
      }
    }
    $agendas = $agendas->get();

    $arrayAgendas = [];
    foreach ($agendas as $agenda) {
      $arrayAgendas[] = $agenda->id;
    }

    $pendientes = 0;
    $resueltos = 0;
    if(count($arrayAgendas) > 0){
      if($request->delegacion_filtro == 1){
        $pendientes = Auditoria::where('estado','1')->whereIn('agenda_id', $arrayAgendas);
        $resueltos = Auditoria::where('estado','2')->whereIn('agenda_id', $arrayAgendas);
      }else if($request->delegacion_filtro == 2){
        $pendientes = Pci::where('estado','1')->whereIn('agenda_id', $arrayAgendas);
        $resueltos = Pci::where('estado','2')->whereIn('agenda_id', $arrayAgendas);
      }

      if($request->has('gestor_filtro')){
        $gestor_filtro = $request->gestor_filtro;
        if($gestor_filtro != 0){
          $pendientes = $pendientes->where('lector_id', $gestor_filtro);
          $resueltos = $resueltos->where('lector_id', $gestor_filtro);
        }
      }
      $pendientes = $pendientes->count();
      $resueltos = $resueltos->count();
    }

    return response()->json([
        'pendientes' => $pendientes,
        'resueltos' => $resueltos
    ]);
  }

  public function getPointMapGestores(Request $request){
    $agendas = Agenda::where('fecha', 'LIKE', DB::raw("'%$request->fecha%'"));
    if($request->has('delegacion_filtro')){
      $delegacion_filtro = $request->delegacion_filtro;
      if($delegacion_filtro != 0){
        $agendas = $agendas->where('tipo_lectura_id', $delegacion_filtro);
      }
    }
    $agendas = $agendas->get();

    $arrayAgendas = [];
    $count = 0;
    $stringIn = '';
    foreach ($agendas as $agenda) {
      $arrayAgendas[] = $agenda->id;
      if($count == 0){
        $stringIn = $agenda->id;
        $count++;
      } else {
        $stringIn .= ',' . $agenda->id;
      }
    }

    $gestores = [];
    if(count($arrayAgendas) > 0){
      if($request->delegacion_filtro == 1){
        $gestores = Auditoria::select(
                DB::raw("u.nombre"),
                DB::raw("(select ar.latitud from auditoria ar where a.lector_id = ar.lector_id and ar.agenda_id in ($stringIn) order by ar.orden_realizado desc limit 1) as lat"),
                DB::raw("(select ar.longitud from auditoria ar where a.lector_id = ar.lector_id and ar.agenda_id in ($stringIn) order by ar.orden_realizado desc limit 1) as lon")
            )
            ->from(DB::raw('auditoria a'))
            ->join('usuarios as u', 'u.id', '=', 'a.lector_id')
            ->whereIn('a.agenda_id', $arrayAgendas);
      }else if($request->delegacion_filtro == 2){
        $gestores = Pci::select(
                DB::raw("u.nombre"),
                DB::raw("(select ar.latitud from pci ar where a.lector_id = ar.lector_id and ar.agenda_id in ($stringIn) order by ar.orden_realizado desc limit 1) as lat"),
                DB::raw("(select ar.longitud from pci ar where a.lector_id = ar.lector_id and ar.agenda_id in ($stringIn) order by ar.orden_realizado desc limit 1) as lon")
            )
            ->from(DB::raw('pci a'))
            ->join('usuarios as u', 'u.id', '=', 'a.lector_id')
            ->whereIn('a.agenda_id', $arrayAgendas);
      }

      if($request->has('gestor_filtro')){
        $gestor_filtro = $request->gestor_filtro;
        if($gestor_filtro != 0){
          $gestores = $gestores->where('a.lector_id', $gestor_filtro);
        }
      }
      if($request->has('estados_filtro')){
        $estados_filtro = $request->estados_filtro;
        if($estados_filtro != 0){
          if($estados_filtro == 2){
            $gestores = $gestores->where('a.estado', '>', 1);
          }else{
            $gestores = $gestores->where('a.estado', $estados_filtro);
          }
        }
      }
      $gestores = $gestores->groupBy('u.nombre', 'lat', 'lon')->get();
    }

    return response()->json([
      'gestores' => $gestores
    ]);
  }
}
