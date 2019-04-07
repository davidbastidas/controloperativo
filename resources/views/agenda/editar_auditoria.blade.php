@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <form action="{{route('servicio.update')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="agenda" value="{{$servicio->agenda_id}}">
                    <input type="hidden" name="servicio" value="{{$servicio->id}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <center><h4>EDITAR SERVICIO</h4></center>
                            </div>
                        </div>
                        <br>

                        <input type="hidden" name="aviso_id" value="{{$servicio->id}}">
                        <div class="row">
                            <div class="col-md-2">
                                Nic: {{$servicio->nic}}
                            </div>
                            <div class="col-md-2">
                                Medidor: {{$servicio->medidor}}
                            </div>
                            <div class="col-md-4">
                                Cliente: {{$servicio->cliente}}
                            </div>

                            <div class="col-md-1">
                            </div>

                            <div class="col-md-3">

                                <button style="margin-bottom: 8px"
                                        class="btn-block btn btn-outline-primary" type="submit">
                                    Guardar <i class="mdi mdi-content-save"></i>
                                </button>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Anomalia</label>
                                <select class="form-control" name="anomalia">
                                    <option value="">Selecciona..</option>
                                    @foreach($anomalias as $anomalia)
                                        @if($anomalia->id == $servicio->anomalia_id)
                                            <option value="{{$anomalia->id}}"
                                                    selected>{{$anomalia->nombre}}</option>
                                        @else
                                            <option
                                                value="{{$anomalia->id}}">{{$anomalia->nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                              <label>Lectura</label>
                              <input class="form-control" type="text" name="lectura" value="{{$servicio->lectura}}">
                            </div>
                            <div class="col-md-2">
                                <label>Habitado</label>
                                <select class="form-control" name="habitado">
                                    <option value="">Selecciona..</option>
                                    @if ($servicio->habitado == 1)
                                      <option value="1" selected>SI</option>
                                      <option value="0">NO</option>
                                    @else
                                      <option value="1">SI</option>
                                      <option value="0" selected>NO</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Anomalia Visible</label>
                                <select class="form-control" name="visible">
                                    <option value="">Selecciona..</option>
                                    @if ($servicio->visible == 1)
                                      <option value="1" selected>SI</option>
                                      <option value="0">NO</option>
                                    @else
                                      <option value="1">SI</option>
                                      <option value="0" selected>NO</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Tipo de Negocio</label>
                                <select class="form-control" name="observacion">
                                    <option value="">Selecciona..</option>
                                    @foreach($observaciones as $observacion)
                                        @if($observacion->id == $servicio->observacion_rapida)
                                            <option value="{{$observacion->id}}"
                                                    selected>{{$observacion->nombre}}</option>
                                        @else
                                            <option
                                                value="{{$observacion->id}}">{{$observacion->nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Observacion Analisis</label>
                                <textarea class="form-control" name="observacion_analisis"
                                          rows="6">{{$servicio->observacion_analisis}}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label>Foto</label>
                                <br>
                                <img src="{{$path}}" height="350px" width="100%">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
