<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\Pci;
use App\Agenda;
use App\LecturasPci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class DownloadController extends Controller
{
    public $servicios = null;


    public function download(Request $request)
    {
        $agenda = $request->agenda;
        $agendaM = Agenda::where('id', $agenda)->first();
        $servicios = null;
        $nombreArchivo = '';
        if($agendaM->tipo_lectura_id == 1){//auditoria
          $nombreArchivo = 'Auditorias';
          $model = new Auditoria();
          $servicios = $model->hydrate(
              DB::select(
                  "call download_auditorias($agenda)"
              )
          );
        }else if($agendaM->tipo_lectura_id == 2){
          $nombreArchivo = 'Pci';
          $model = new Pci();
          $servicios = $model->hydrate(
              DB::select(
                  "call download_pci($agenda)"
              )
          );
          $array = [];
          foreach ($servicios as $serv) {
            $servicio = Pci::where('id', $serv->ITEM)->first();
            $mensajeCritica = '';
            $desviacion = '';
            $desviacionNum = -1;
            if($servicio->estado > 1){
              $critica = true;
              $ultimasLecturas = LecturasPci::where('pci', $servicio->medidor)
                                      ->where('fecha', '>=',DB::raw("'" . $servicio->fecha_recibido . "'-interval 3 month"))
                                      ->orderBy('fecha')->get();
              $lectura1 = -1;
              $lectura2 = -1;
              $consumoActual = -1;
              $consumoAnterior = -1;
              $desviacion = -1;
              if(count($ultimasLecturas) >= 2){
                $count = 1;
                foreach ($ultimasLecturas as $l) {
                  if($count == 1){
                    $lectura1 = $l->lectura;
                    $count++;
                  }else{
                    $lectura2 = $l->lectura;break;
                  }
                }
                $consumoActual = $servicio->lectura - $lectura1;
                $consumoAnterior = $lectura1 - $lectura2;
                $desviacion = 0;
                if($consumoAnterior > 0){
                  $desviacion = ceil((($consumoAnterior - $consumoActual)/$consumoAnterior)*100);
                }
                $desviacionNum = $desviacion;
                $desviacion .= '%';
              }
              $ultimomes = LecturasPci::where('pci', $servicio->medidor)->orderByDesc('fecha')->first();
              $ultimaLectura = -1;
              $ultimaAnom = '';
              if(isset($ultimomes->id)){
                $ultimaLectura = $ultimomes->lectura;
                $ultimaAnom = $ultimomes->anomalia;
              }
              //critica
              if($servicio->lectura == 0){
                $mensajeCritica = 'LECTURA CERO';
              }else if($ultimaLectura == -1){
                $mensajeCritica = 'SIN LECTURA ANTERIOR';
              }else if($servicio->lectura > $ultimaLectura){
                $mensajeCritica = 'LECTURA OK ' . $ultimaLectura;
              }else if($servicio->lectura == $ultimaLectura){
                $mensajeCritica = 'LECTURA REPETIDA';
              }else{
                $mensajeCritica = 'LECTURA MENOR ' . $ultimaLectura;
              }
              if($servicio->anomalia_id == 45){//SIN ANOMALIA EL CODIGO EN LA TABLA
                $mensajeCritica .= '';
              }else if($servicio->anomalia->nombre == $ultimaAnom){
                $mensajeCritica .= ' - ANOMALIA IGUAL ' . $ultimaAnom;
              }else if($ultimaAnom == ''){
                $mensajeCritica .= ' - SIN ANOMALIA ANTERIOR';
              }else{
                $mensajeCritica .= ' - ANOMALIA DIFERENTE ' . $ultimaAnom;
              }
              if($desviacion == -1){
                $mensajeCritica .= ' - Desviacion SIN CALCULAR';
              }else{
                $mensajeCritica .= ' - Desviacion ' . $desviacion;
              }
            }
            array_push($array, array(
              'ITEM' => $serv->ITEM,
              'CT' => $serv->CT,
              'MT' => $serv->MT,
              'DIRECCION' => $serv->DIRECCION,
              'MEDIDOR' => $serv->MEDIDOR,
              'MEDIDOR_ANTERIOR' => $serv->MEDIDOR_ANTERIOR,
              'MEDIDOR_PORTERIOR' => $serv->MEDIDOR_PORTERIOR,
              'BARRIO' => $serv->BARRIO,
              'LECTURA' => $serv->LECTURA,
              'ANOMALIA' => $serv->ANOMALIA,
              'OBSERVACIONES' => $serv->OBSERVACIONES,
              'MUNICPIO' => $serv->MUNICPIO,
              'CODIGO' => $serv->CODIGO,
              'AN_ANTERIOR' => $serv->AN_ANTERIOR,
              'LECTURA_ANTERIOR' => $serv->LECTURA_ANTERIOR,
              'UNICOM' => $serv->UNICOM,
              'RUTA' => $serv->RUTA,
              'ITIN' => $serv->ITIN,
              'FECHA_ENTREGADO' => $serv->FECHA_ENTREGADO,
              'ESTADO' => $serv->ESTADO,
              'FECHA_ENVIO_TEL' => $serv->FECHA_ENVIO_TEL,
              'FECHA_SERVIDOR' => $serv->FECHA_SERVIDOR,
              'FECHA_REALIZAR' => $serv->FECHA_REALIZAR,
              'OPERARIO' => $serv->OPERARIO,
              'HORA_SERVIDOR' => $serv->HORA_SERVIDOR,
              'HORA_TELEFONO' => $serv->HORA_TELEFONO,
              'PUNTO_GPS' => $serv->PUNTO_GPS,
              'CRITICA' => $mensajeCritica,
              'DESVIACION' => $desviacionNum
            ));
          }
          $servicios = $array;
        }

        $this->servicios = $servicios;

        Excel::create($nombreArchivo, function ($excel) {

            $servicios = $this->servicios;

            $excel->sheet('Auditorias', function ($sheet) use ($servicios) {

                $sheet->fromArray($servicios);

            });

        })->export('xlsx');
    }
}
