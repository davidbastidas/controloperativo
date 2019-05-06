@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if(count($lectores) > 0)
                        <div class="row">
                            <div class="col-md-10">
                                <h4>Asignar Servicios {{$agendaModel->codigo}} de {{$agendaModel->fecha}}</h4>
                            </div>
                            <div class="col-md-2">
                                <form action="{{route('agenda.vaciarcarga')}}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="agenda" value="{{$agenda}}">
                                    <button class="btn btn-danger" type="submit">Vaciar Carga</button>
                                </form>
                            </div>
                        </div>

                        @if(isset($success))
                            <div class="alert alert-success" role="alert">
                                <strong>{{$success}}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{route('agenda.asignar')}}" method="post">
                                    <input type="hidden" name="agenda" value="{{$agenda}}">
                                    <div class="form-group">
                                        <label>Lector Cargado</label>
                                        <select name="gestor" class="form-control">
                                            @foreach($lectores as $usu)
                                                <option value="{{$usu->lector}}">{{$usu->lector}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Lector a Asignar</label>
                                        <select name="user" class="form-control">
                                            @foreach($usuarios as $user)
                                                <option value="{{$user->id}}">{{$user->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="btn btn-success mr-2" type="submit">
                                        Asignar Uno
                                    </button>
                                </form>
                                <br>
                                <form action="{{route('agenda.asignarall')}}" method="post">
                                    <input type="hidden" name="agenda" value="{{$agenda}}">
                                    <button class="btn btn-outline-info" type="submit">Asignar Todo</button>
                                </form>
                            </div>
                        </div>
                        <hr>
                    @endif

                    <div class="row">
                        <div class="col-md-10">
                            <h4>Lista de Servicios {{$agendaModel->codigo}} de {{$agendaModel->fecha}}</h4>
                        </div>
                    </div>
                    @php
                        $colorBar = 'danger';
                        $porcentaje = 0;
                        if(($pendientes + $realizados) > 0){
                          $porcentaje = round((100 * $realizados) / ($pendientes + $realizados));
                          if($porcentaje < 20){
                            $colorBar = 'danger';
                          } else if($porcentaje >= 20 && $porcentaje < 50){
                            $colorBar = 'warning';
                          } else if($porcentaje >= 50 && $porcentaje < 70){
                            $colorBar = 'info';
                          } else if($porcentaje >= 70 && $porcentaje < 100){
                            $colorBar = 'primary';
                          } else if($porcentaje == 100){
                            $colorBar = 'success';
                          }
                        }
                    @endphp
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="wrapper d-flex justify-content-between">
                                <div class="side-left">
                                    <p class="mb-2">Realizados</p>
                                    <p class="display-4 mb-4 font-weight-light text-success">
                                        @if ($realizados == 0)
                                            {{$realizados}}
                                        @else
                                            {{$realizados}}
                                            <i class="mdi mdi-arrow-up"></i>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="wrapper d-flex justify-content-between">
                                <div class="side-left">
                                    <p class="mb-2">Pendientes</p>
                                    <p class="display-4 mb-4 font-weight-light text-danger">
                                        @if ($pendientes == 0)
                                            {{$pendientes}}
                                        @else
                                            {{$pendientes}}
                                            <i class="mdi mdi-arrow-down"></i>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">Avance {{$porcentaje.'%'}}</p>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{$colorBar}}"
                                     role="progressbar" style="width: {{$porcentaje}}%" aria-valuenow="{{$porcentaje}}"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="sr-only">Borrar Masivo</label>
                            @if ($pendientes > 0)
                                <input type="hidden" id="agenda_id" value="{{$agenda}}">
                                <button class="btn btn-danger mb-2" type="submit" id="borrar_masivo">Borrar Masivo
                                </button>
                                <div id="form-hidden" style="display: none;"></div>
                            @endif
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-inline" action="{{route('agenda.detalle',['agenda' => $agenda])}}"
                                  method="get">
                                <label class="sr-only">Lector</label>
                                <select name="gestor_filtro" class="form-control mb-2 mr-sm-2">
                                    <option value="0">[Todos los Lectores]</option>
                                    @foreach($lectoresAsignados as $gestor)
                                        @foreach ($usuarios as $usuario)
                                            @if ($usuario->id == $gestor->lector_id)
                                                @if ($gestor_filtro == $usuario->id)
                                                    <option value="{{$usuario->id}}"
                                                            selected>{{$usuario->nombre}}</option>
                                                @else
                                                    <option value="{{$usuario->id}}">{{$usuario->nombre}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endforeach
                                </select>

                                <label class="sr-only">Estado</label>
                                <select name="estados_filtro" class="form-control mb-2 mr-sm-2">
                                    <option value="0">[Todos los Estados]</option>
                                    <option value="1" @if ($estados_filtro == 1) selected @endif>PENDIENTES</option>
                                    <option value="2" @if ($estados_filtro == 2) selected @endif>REALIZADOS</option>
                                    <option value="3" @if ($estados_filtro == 3) selected @endif>MODIFICADOS</option>
                                </select>

                                @if ($agendaModel->tipo_lectura_id == 1)
                                  <label class="sr-only">NIC</label>
                                  <input type="text" class="form-control mb-2 mr-sm-2" name="nic_filtro" placeholder="NIC"
                                         value="{{$nic_filtro}}">
                                @endif


                                <label class="sr-only">MEDIDOR</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" name="medidor_filtro"
                                       placeholder="MEDIDOR" value="{{$medidor_filtro}}">

                                <button class="btn btn-success mb-2" type="submit">Filtrar</button>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table style="width: 100%;text-align:center;font-size: 12px;" class="table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;padding: 10px;">
                                            @if ($pendientes == 0)
                                                #
                                            @else
                                                <input type="checkbox" id="avisos-check-all">
                                            @endif
                                        </th>
                                        <th style="width: 20%;">Lector</th>
                                        <th style="width: 15%;">Barrio</th>
                                        @if ($agendaModel->tipo_lectura_id == 1)
                                          <th style="width: 10%;">NIC</th>
                                        @elseif ($agendaModel->tipo_lectura_id == 2)
                                          <th style="width: 10%;">MEDIDOR</th>
                                        @endif
                                        <th style="width: 10%;">Anomalia.</th>
                                        <th style="width: 10%;">Accion</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($servicios as $serv)
                                        <tr>
                                            <td>
                                                @if ($serv->estado == 1)
                                                    <input type="checkbox" class="check-avisos" name="avisos[]"
                                                           value="{{ $serv->id }}">
                                                @else
                                                    {{$count++}}
                                                @endif
                                            </td>
                                            <td>{{ $serv->usuario->nombre }}</td>
                                            <td>{{ $serv->barrio }}</td>
                                            @if ($agendaModel->tipo_lectura_id == 1)
                                              <td>{{ $serv->nic }}</td>
                                            @elseif ($agendaModel->tipo_lectura_id == 2)
                                              <td>{{ $serv->medidor }}</td>
                                            @endif
                                            <td>
                                                @if (isset($serv->anomalia->nombre))
                                                    {{ $serv->anomalia->nombre }}
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{route('servicio.editar', ['agenda' => $agendaModel->id, 'servicio' => $serv->id])}}">
                                                    <button style="margin-bottom: 8px"
                                                            class="btn-sm btn btn-outline-primary">
                                                        Ver <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                </form>
                                                @if ($serv->estado == 1)
                                                    <form action="{{route('servicio.eliminar', ['agenda' => $agendaModel->id, 'servicio' => $serv->id])}}">
                                                        <button style="margin-bottom: 8px"
                                                                class="btn-sm btn btn-outline-danger">
                                                            Eliminar <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <br>
                                {{ $servicios->appends([])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
