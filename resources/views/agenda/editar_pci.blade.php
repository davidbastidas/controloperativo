@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
              @if($critica)
                <div class="card-body">
                  <div class="row">
                      <div class="col-md-10">
                          CRITICA: {{$mensajeCritica}}
                      </div>

                      <div class="col-md-2">
                          <button style="margin-bottom: 8px"
                                  class="btn-block btn btn-outline-success" type="submit">
                              Aprobar <i class="mdi mdi-content-save"></i>
                          </button>
                      </div>
                  </div>
                </div>
              @endisset
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
                              CT: {{$servicio->ct}}
                          </div>
                          <div class="col-md-2">
                              MT: {{$servicio->mt}}
                          </div>
                          <div class="col-md-3">
                              Medidor: {{$servicio->medidor}}
                          </div>
                          <div class="col-md-3">
                              Direccion: {{$servicio->direccion}}
                          </div>

                          <div class="col-md-2">
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
                            <label>Lectura Actual</label>
                            <input class="form-control" type="text" name="lectura" value="{{$servicio->lectura}}">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-3">
                              <label>Observacion Analisis</label>
                              <textarea class="form-control" name="observacion_analisis"
                                        rows="14">{{$servicio->observacion_analisis}}</textarea>
                          </div>

                          <div class="col-md-4">
                              <label>Foto</label>
                              <br>
                              <img src="{{$path}}" height="350px" width="100%">
                          </div>
                          <div class="col-md-5">
                              @if ($ultimasLecturas != null)
                                <label>Lecturas Encontradas</label>
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>Fecha</th>
                                      <th>Lectura</th>
                                      <th>Anomalia</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach ($ultimasLecturas as $key)
                                      <tr>
                                        @php
                                          $fechaCarbon = new \Carbon\Carbon($key->fecha);
                                        @endphp
                                        <td>{{$fechaCarbon->format('d/m/Y')}}</td>
                                        <td>{{$key->lectura}}</td>
                                        <td>{{$key->anomalia}}</td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              @else
                                <label>No se encontraron lecturas en la Historia</label>
                              @endif
                          </div>
                      </div>
                  </div>
              </form>
            </div>
        </div>
    </div>
@endsection
