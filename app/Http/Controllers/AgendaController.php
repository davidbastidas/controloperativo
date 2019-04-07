<?php

namespace App\Http\Controllers;

use App\AdminTable;
use App\Agenda;
use App\Anomalias;
use App\Auditoria;
use App\AuditoriaTemp;
use App\Pci;
use App\PciTemp;
use App\TipoLectura;
use App\ObservacionesRapidas;
use App\User;
use App\Usuarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;


class AgendaController extends Controller
{
    public function index()
    {
        $tiposLectura = TipoLectura::all();

        $perPage = 6;
        $page = Input::get('page');
        $pageName = 'page';
        $page = Paginator::resolveCurrentPage($pageName);
        $offSet = ($page * $perPage) - $perPage;

        $agenda = new Agenda();

        $agendas = $agenda->offset($offSet)->limit($perPage)->orderByDesc('id')->get();

        $total_registros = Agenda::all()->count();
        $array = [];
        $agendaCollection = null;

        foreach ($agendas as $agenda) {

            $user = User::where('id', $agenda->admin_id)->first()->name;

            $pendientes = 0;
            $realizados = 0;
            $cargasPendientes = 0;
            if($agenda->tipo_lectura_id == 1){
              $pendientes = Auditoria::where('estado', 1)->where('agenda_id', $agenda->id)->count();
              $realizados = Auditoria::where('estado', '>', 1)->where('agenda_id', $agenda->id)->count();
              $cargasPendientes = AuditoriaTemp::where('agenda_id', $agenda->id)->count();
            } elseif($agenda->tipo_lectura_id == 2){
              $pendientes = Pci::where('estado', 1)->where('agenda_id', $agenda->id)->count();
              $realizados = Pci::where('estado', '>', 1)->where('agenda_id', $agenda->id)->count();
              $cargasPendientes = PciTemp::where('agenda_id', $agenda->id)->count();
            }

            $flag = false;

            if ($pendientes > 0){
                $flag = true;
            }
            if ($cargasPendientes > 0){
                $flag = true;
            }
            if ($realizados > 0){
                $flag = true;
            }

            array_push($array, (object)array(
                'id' => $agenda->id,
                'codigo' => $agenda->codigo,
                'fecha' => $agenda->fecha,
                'tipo_lectura_id' => $agenda->tipo_lectura_id,
                'usuario' => $user,
                'pendientes' => $pendientes,
                'realizados' => $realizados,
                'cargasPendientes' => $cargasPendientes,
                'flag' => $flag
            ));
        }

        $agendaCollection = new Collection($array);

        $posts = new LengthAwarePaginator($agendaCollection, $total_registros, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return view('agenda.agenda',[
          'tiposLectura' => $tiposLectura,
          'agendas' => $posts
        ])->withPosts($posts);
    }

    public function saveAgenda(Request $request)
    {

        $agenda = new Agenda();
        $agenda->fecha = $request->fecha;
        $agenda->tipo_lectura_id = $request->delegacion;
        $agenda->admin_id = Auth::user()->id;

        $agenda->save();

        $anio = Carbon::now()->year;

        $agenda->codigo = "AGE-" . $agenda->id . "-" . $anio;

        $agenda->save();

        return redirect()->route('agenda');
    }

    public function viewUpload($id_agenda)
    {
        $agenda = Agenda::where('id', $id_agenda)->first();

        $fecha = explode(' ', $agenda->fecha)[0];

        return view('agenda.upload', ['agenda' => $agenda, 'fecha' =>$fecha]);
    }

    public function subirServicios(Request $request)
    {
        $archivo = $request->file;
        $agenda = Agenda::where('id', $request->agenda)->first();
        $results = Excel::load($archivo)->all()->toArray();
        foreach ($results as $row) {
            foreach ($row as $x => $x_value) {
                $base = [];
                $count = 0;
                foreach ($x_value as $y => $y_value) {
                    $base[$count] = $y_value;
                    $count++;
                }
                if($agenda->tipo_lectura_id == 1){
                  $servicio = new AuditoriaTemp();
                  $servicio->barrio = $base[1];
                  $servicio->localidad = $base[2];
                  $servicio->cliente = $base[3];
                  $servicio->direccion = $base[4];
                  $servicio->nic = $base[5];
                  $servicio->ruta = $base[6];
                  $servicio->itin = $base[7];
                  $servicio->medidor = $base[8];
                  $servicio->motivo = $base[9];
                  $servicio->nis = $base[10];
                  $servicio->lector = $base[11];
                  $servicio->an_anterior = $base[12];
                  $servicio->lectura_anterior = $base[13];
                  $servicio->pide_foto = $base[14];
                  $servicio->pide_gps = $base[15];
                  $servicio->admin_id = Auth::user()->id;
                  $servicio->agenda_id = $agenda->id;
                  $servicio->save();
                }elseif($agenda->tipo_lectura_id == 2){
                  $servicio = new PciTemp();
                  $servicio->ct = $base[1];
                  $servicio->mt = $base[2];
                  $servicio->direccion = $base[3];
                  $servicio->medidor = $base[4];
                  $servicio->medidor_anterior = $base[5];
                  $servicio->medidor_posterior = $base[6];
                  $servicio->barrio = $base[7];
                  $servicio->municipio = $base[8];
                  $servicio->codigo = $base[9];
                  $servicio->unicom = $base[10];
                  $servicio->ruta = $base[11];
                  $servicio->itin = $base[12];
                  $servicio->lector = $base[13];
                  $servicio->an_anterior = $base[14];
                  $servicio->lectura_anterior = $base[15];
                  $servicio->fecha_entrega = $base[16]->format('Y-m-d');
                  $servicio->pide_foto = $base[17];
                  $servicio->pide_gps = $base[18];
                  $servicio->admin_id = Auth::user()->id;
                  $servicio->agenda_id = $agenda->id;
                  $servicio->save();
                }
            }
        }
        return \Redirect::route('agenda');
    }

    public function listar($agenda)
    {
        $lector_filtro = 0;
        $estados_filtro = 0;
        $nic_filtro = '';
        $medidor_filtro = '';

        $agendaModel = Agenda::find($agenda);
        $lectores = null;
        if($agendaModel->tipo_lectura_id == 1){
          $lectores = AuditoriaTemp::select('lector')->where('agenda_id', $agenda)->groupBy('lector')->get();
        }elseif($agendaModel->tipo_lectura_id == 2){
          $lectores = PciTemp::select('lector')->where('agenda_id', $agenda)->groupBy('lector')->get();
        }
        $usuarios = Usuarios::all();

        $perPage = 150;
        $page = Input::get('page');
        $pageName = 'page';
        $page = Paginator::resolveCurrentPage($pageName);
        $offSet = ($page * $perPage) - $perPage;

        $servicios = null;
        $serviciosAux1 = null;
        $serviciosAux2 = null;
        if($agendaModel->tipo_lectura_id == 1){
          $servicios = Auditoria::where('agenda_id', $agenda);
          $serviciosAux1 = Auditoria::where('agenda_id', $agenda);
          $serviciosAux2 = Auditoria::where('agenda_id', $agenda);
        }elseif($agendaModel->tipo_lectura_id == 2){
          $servicios = Pci::where('agenda_id', $agenda);
          $serviciosAux1 = Pci::where('agenda_id', $agenda);
          $serviciosAux2 = Pci::where('agenda_id', $agenda);
        }

        if(Input::has('gestor_filtro')){
            $lector_filtro = Input::get('gestor_filtro');
            if($lector_filtro != 0){
                $servicios = $servicios->where('lector_id', $lector_filtro);
                $serviciosAux1 = $serviciosAux1->where('lector_id', $lector_filtro);
                $serviciosAux2 = $serviciosAux2->where('lector_id', $lector_filtro);
            }
        }
        if(Input::has('estados_filtro')){
            $estados_filtro = Input::get('estados_filtro');
            if($estados_filtro != 0){
                $servicios = $servicios->where('estado', $estados_filtro);
                $serviciosAux1 = $serviciosAux1->where('estado', $estados_filtro);
                $serviciosAux2 = $serviciosAux2->where('estado', $estados_filtro);
            }
        }
        if(Input::has('nic_filtro')){
            $nic_filtro = Input::get('nic_filtro');
            if($nic_filtro != 0){
                $servicios = $servicios->where('nic', $nic_filtro);
                $serviciosAux1 = $serviciosAux1->where('nic', $nic_filtro);
                $serviciosAux2 = $serviciosAux2->where('nic', $nic_filtro);
            }
        }
        if(Input::has('medidor_filtro')){
            $medidor_filtro = Input::get('medidor_filtro');
            if($medidor_filtro != 0){
                $servicios = $servicios->where('medidor', DB::raw("'$medidor_filtro'"));
                $serviciosAux1 = $serviciosAux1->where('medidor', DB::raw("'$medidor_filtro'"));
                $serviciosAux2 = $serviciosAux2->where('medidor', DB::raw("'$medidor_filtro'"));
            }
        }
        $serviciosAux = $servicios;
        $total_registros = $serviciosAux->count();
        $pendientes = $serviciosAux1->where('estado',  '=', DB::raw("1"))->count();
        $realizados = $serviciosAux2->where('estado', '>', DB::raw("1"))->count();
        $servicios = $servicios->offset($offSet)->limit($perPage)->orderBy('id')->get();

        $posts = new LengthAwarePaginator($servicios, $total_registros, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        $lectoresAsignados = null;
        if($agendaModel->tipo_lectura_id == 1){
          $lectoresAsignados = Auditoria::select('lector_id')->where('agenda_id', $agenda)->groupBy('lector_id')->get();
        }elseif($agendaModel->tipo_lectura_id == 2){
          $lectoresAsignados = Pci::select('lector_id')->where('agenda_id', $agenda)->groupBy('lector_id')->get();
        }
        return view('agenda.detalle', [
            'lectores' => $lectores,
            'usuarios' => $usuarios,
            'agenda' => $agenda,
            'agendaModel' => $agendaModel,
            'servicios' => $posts,
            'lectoresAsignados' => $lectoresAsignados,
            'pendientes' => $pendientes,
            'realizados' => $realizados,
            'gestor_filtro' => $lector_filtro,
            'estados_filtro' => $estados_filtro,
            'nic_filtro' => $nic_filtro,
            'medidor_filtro' => $medidor_filtro
        ]);
    }

    public function asignarUnoAUno(Request $request)
    {
        $agenda = Agenda::find($request->agenda);
        $lector = $request->gestor;
        $user = $request->user;

        $servicios = null;
        if($agenda->tipo_lectura_id == 1){
          $servicios = AuditoriaTemp::where('lector', $lector)->where('agenda_id', $agenda->id)->get();
        }elseif($agenda->tipo_lectura_id == 2){
          $servicios = PciTemp::where('lector', $lector)->where('agenda_id', $agenda->id)->get();
        }

        foreach ($servicios as $servicio) {
          $serv = null;
          if($agenda->tipo_lectura_id == 1){
            $serv = new Auditoria();
            $serv->barrio = $servicio->barrio;
            $serv->localidad = $servicio->localidad;
            $serv->cliente = $servicio->cliente;
            $serv->direccion = $servicio->direccion;
            $serv->nic = $servicio->nic;
            $serv->ruta = $servicio->ruta;
            $serv->itin = $servicio->itin;
            $serv->medidor = $servicio->medidor;
            $serv->motivo = $servicio->motivo;
            $serv->nis = $servicio->nis;
            $serv->lector = $servicio->lector;
            $serv->an_anterior = $servicio->an_anterior;
            $serv->lectura_anterior = $servicio->lectura_anterior;
            $serv->pide_foto = $servicio->pide_foto;
            $serv->pide_gps = $servicio->pide_gps;
            $serv->orden_realizado = 0;
            $serv->estado = 1;
            $serv->lector_id = $user;
            $serv->admin_id = Auth::user()->id;
            $serv->agenda_id = $agenda->id;
            $serv->pide_gps = 1;
          }elseif($agenda->tipo_lectura_id == 2){
            $serv = new Pci();
            $serv->ct = $servicio->ct;
            $serv->mt = $servicio->mt;
            $serv->direccion = $servicio->direccion;
            $serv->medidor = $servicio->medidor;
            $serv->medidor_anterior = $servicio->medidor_anterior;
            $serv->medidor_posterior = $servicio->medidor_posterior;
            $serv->barrio = $servicio->barrio;
            $serv->municipio = $servicio->municipio;
            $serv->codigo = $servicio->codigo;
            $serv->an_anterior = $servicio->an_anterior;
            $serv->lectura_anterior = $servicio->lectura_anterior;
            $serv->unicom = $servicio->unicom;
            $serv->ruta = $servicio->ruta;
            $serv->itin = $servicio->itin;
            $serv->fecha_entrega = $servicio->fecha_entrega;
            $serv->pide_foto = $servicio->pide_foto;
            $serv->pide_gps = $servicio->pide_gps;
            $serv->lector = $servicio->lector;
            $serv->orden_realizado = 0;
            $serv->estado = 1;
            $serv->lector_id = $user;
            $serv->admin_id = Auth::user()->id;
            $serv->agenda_id = $agenda->id;
            $serv->pide_gps = 1;
          }
          try {
              $serv->save();
              $servicio->delete();
          } catch (\Exception $e) {
          }
        }

        return redirect()->route('agenda.detalle', ['agenda' => $agenda->id]);
    }

    public function asignarAll(Request $request)
    {
        $agenda = Agenda::find($request->agenda);
        $lectoresTemp = null;
        if($agenda->tipo_lectura_id == 1){
          $lectoresTemp = AuditoriaTemp::select('lector')->where('agenda_id', $agenda->id)->groupBy('lector')->get();
        }elseif($agenda->tipo_lectura_id == 2){
          $lectoresTemp = PciTemp::select('lector')->where('agenda_id', $agenda->id)->groupBy('lector')->get();
        }

        foreach ($lectoresTemp as $ges) {
            $gestor = explode(" ", $ges->lector);
            $cedula = trim($gestor[0]);

            $serviciosTemp = null;
            if($agenda->tipo_lectura_id == 1){
              $serviciosTemp = AuditoriaTemp::where('lector', $ges->lector)->where('agenda_id', $agenda->id)->get();
            }elseif($agenda->tipo_lectura_id == 2){
              $serviciosTemp = PciTemp::where('lector', $ges->lector)->where('agenda_id', $agenda->id)->get();
            }

            $usuario = Usuarios::where('nickname', $cedula)->first();
            foreach ($serviciosTemp as $servicio) {
              $serv = null;
              if($agenda->tipo_lectura_id == 1){
                $serv = new Auditoria();
                $serv->barrio = $servicio->barrio;
                $serv->localidad = $servicio->localidad;
                $serv->cliente = $servicio->cliente;
                $serv->direccion = $servicio->direccion;
                $serv->nic = $servicio->nic;
                $serv->ruta = $servicio->ruta;
                $serv->itin = $servicio->itin;
                $serv->medidor = $servicio->medidor;
                $serv->motivo = $servicio->motivo;
                $serv->nis = $servicio->nis;
                $serv->lector = $servicio->lector;
                $serv->an_anterior = $servicio->an_anterior;
                $serv->lectura_anterior = $servicio->lectura_anterior;
                $serv->pide_foto = $servicio->pide_foto;
                $serv->pide_gps = $servicio->pide_gps;
                $serv->orden_realizado = 0;
                $serv->estado = 1;
                $serv->lector_id = $usuario->id;
                $serv->admin_id = Auth::user()->id;
                $serv->agenda_id = $agenda->id;
                $serv->pide_gps = 1;
              }elseif($agenda->tipo_lectura_id == 2){
                $serv = new Pci();
                $serv->ct = $servicio->ct;
                $serv->mt = $servicio->mt;
                $serv->direccion = $servicio->direccion;
                $serv->medidor = $servicio->medidor;
                $serv->medidor_anterior = $servicio->medidor_anterior;
                $serv->medidor_posterior = $servicio->medidor_posterior;
                $serv->barrio = $servicio->barrio;
                $serv->municipio = $servicio->municipio;
                $serv->codigo = $servicio->codigo;
                $serv->an_anterior = $servicio->an_anterior;
                $serv->lectura_anterior = $servicio->lectura_anterior;
                $serv->unicom = $servicio->unicom;
                $serv->ruta = $servicio->ruta;
                $serv->itin = $servicio->itin;
                $serv->fecha_entrega = $servicio->fecha_entrega;
                $serv->pide_foto = $servicio->pide_foto;
                $serv->pide_gps = $servicio->pide_gps;
                $serv->lector = $servicio->lector;
                $serv->orden_realizado = 0;
                $serv->estado = 1;
                $serv->lector_id = $usuario->id;
                $serv->admin_id = Auth::user()->id;
                $serv->agenda_id = $agenda->id;
                $serv->pide_gps = 1;
              }

              try {
                $serv->save();
                $servicio->delete();
              } catch (\Exception $e) {
              }
            }
        }
        return redirect()->route('agenda.detalle', ['agenda' => $agenda->id]);
    }

    public function vaciarCarga(Request $request)
    {
        $id = Auth::user()->id;
        $agenda = Agenda::find($request->agenda);
        if($agenda->tipo_lectura_id == 1){
          AuditoriaTemp::where('admin_id', $id)->where('agenda_id', $request->agenda)->delete();
        }elseif($agenda->tipo_lectura_id == 2){
          PciTemp::where('admin_id', $id)->where('agenda_id', $request->agenda)->delete();
        }

        return redirect()->route('agenda.detalle', ['agenda' => $agenda->id]);
    }

    public function deleteAgenda($agenda)
    {
        $agenda = Agenda::where('id', $agenda)->first();

        $pendientes = 0;
        $realizados = 0;
        $cargasPendientes = 0;
        if($agenda->tipo_lectura_id == 1){
          $pendientes = Auditoria::where('estado', 1)->where('agenda_id', $agenda->id)->count();
          $realizados = Auditoria::where('estado', '>', 1)->where('agenda_id', $agenda->id)->count();
          $cargasPendientes = AuditoriaTemp::where('agenda_id', $agenda->id)->count();
        } elseif($agenda->tipo_lectura_id == 2){
          $pendientes = Pci::where('estado', 1)->where('agenda_id', $agenda->id)->count();
          $realizados = Pci::where('estado', '>', 1)->where('agenda_id', $agenda->id)->count();
          $cargasPendientes = PciTemp::where('agenda_id', $agenda->id)->count();
        }

        $flag = true;

        if ($pendientes > 0 || $cargasPendientes > 0 || $realizados > 0){
            $flag = false;
        }
        if ($flag){
            $agenda->delete();
        }

        return \Redirect::route('agenda');
    }

    public function viewServicio($agenda, $servicio_id){
      $agenda = Agenda::where('id', $agenda)->first();
      $servicio = null;
      $path = '';
      $view = '';
      if($agenda->tipo_lectura_id == 1){
        $servicio = Auditoria::where('id', $servicio_id)->first();
        $filename = $servicio->id . ".png";
        $path = config('myconfig.public_fotos_auditoria')  . $filename;
        $view = 'agenda.editar_auditoria';
      } elseif($agenda->tipo_lectura_id == 2){
        $servicio = Pci::where('id', $servicio_id)->first();
        $filename = $servicio->id . ".png";
        $path = config('myconfig.public_fotos_pci')  . $filename;
        $view = 'agenda.editar_pci';
      }
      $anomalias = Anomalias::all();
      $observaciones = ObservacionesRapidas::all();

      return view($view, [
          'servicio' => $servicio,
          'agenda' => $agenda,
          'anomalias' => $anomalias,
          'observaciones' => $observaciones,
          'path' => $path
      ]);
    }

    public function saveAviso(Request $request){
      $agenda = Agenda::where('id', $request->agenda)->first();
      if($agenda->tipo_lectura_id == 1){
        $servicio = Auditoria::where('id', $request->servicio)->first();
        $servicio->anomalia_id = $request->anomalia;
        $servicio->lectura = $request->lectura;
        $servicio->habitado = $request->habitado;
        $servicio->visible = $request->visible;
        $servicio->observacion_rapida = $request->observacion;
        $servicio->observacion_analisis = $request->observacion_analisis;
        $servicio->estado = 3;
        $servicio->save();
      } elseif($agenda->tipo_lectura_id == 2){
        $servicio = Pci::where('id', $request->servicio)->first();
        $servicio->anomalia_id = $request->anomalia;
        $servicio->lectura = $request->lectura;
        $servicio->observacion_analisis = $request->observacion_analisis;
        $servicio->estado = 3;
        $servicio->save();
      }

      return redirect()->route('agenda.detalle', ['agenda' => $request->agenda]);
    }


    public function deleteServicio($agenda, $servicio){
      $agenda = Agenda::where('id', $agenda)->first();
      if($agenda->tipo_lectura_id == 1){
        Auditoria::where('id', $servicio)->where('estado', 1)->delete();
      } elseif($agenda->tipo_lectura_id == 2){
        Pci::where('id', $servicio)->where('estado', 1)->delete();
      }
      return redirect()->route('agenda.detalle', ['id' => $agenda]);
    }

    public function deleteServicioPorSeleccion(Request $request){
        $arrayIdAvisos = null;
        if ($request->has('avisos')) {
            $arrayIdAvisos = $request->get('avisos');
        }
        $agenda_id = $request->agenda_id;

        if($arrayIdAvisos != null){
          $agenda = Agenda::where('id', $agenda_id)->first();
          if($agenda->tipo_lectura_id == 1){
            Auditoria::whereIn('id', $arrayIdAvisos)->where('estado', 1)->delete();
          } elseif($agenda->tipo_lectura_id == 2){
            Pci::whereIn('id', $arrayIdAvisos)->where('estado', 1)->delete();
          }
        }
        return redirect()->route('agenda.detalle', ['id' => $agenda_id]);
    }

    public function visitaMapa() {
        $usuarios = Usuarios::orderBy('nombre')->get();
        return view('geo.mapas', [
            'usuarios' => $usuarios
        ]);
    }

    public function getPointMapVisita(Request $request){
        $agendas = Agenda::where('fecha', 'LIKE', DB::raw("'%$request->fecha%'"))->get();
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

        $puntos = [];
        if(count($arrayAgendas) > 0){
            $puntos = Avisos::whereIn('agenda_id', $arrayAgendas)
                ->where('gestor_id', $request->gestor_id)
                ->where('estado', '>', 1)
                ->where('latitud', '!=', '0.0')
                ->orderBy('orden_realizado')->get();
        }

        return response()->json([
            'puntos' => $puntos
        ]);
    }
}
