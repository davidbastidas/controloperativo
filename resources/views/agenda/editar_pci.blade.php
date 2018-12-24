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
                            <div class="col-md-1">
                                CT: {{$servicio->ct}}
                            </div>
                            <div class="col-md-1">
                                MT: {{$servicio->mt}}
                            </div>
                            <div class="col-md-2">
                                Medidor: {{$servicio->medidor}}
                            </div>
                            <div class="col-md-4">
                                Direccion: {{$servicio->direccion}}
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
