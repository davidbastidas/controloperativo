@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <form action="{{route('usuarioste.save')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="usuario" value="{{$usuario->id}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <center><h4>EDITAR USUARIO TERRENO</h4></center>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-2">
                              <label>Nombre</label>
                              <input type="text" name="nombre" value="{{$usuario->nombre}}" class="form-control">
                            </div>
                            <div class="col-md-2">
                              <label>Nickname</label>
                              <input type="text" name="nickname" value="{{$usuario->nickname}}" class="form-control">
                            </div>
                            <div class="col-md-2">
                              <label>Contraseña</label>
                              <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-2">
                              <label>Tipo</label>
                              <select class="form-control" name="tipo" class="form-control">
                                  <option value="">Selecciona..</option>
                                  @foreach($tipos as $tipo)
                                      @if($tipo->id == $usuario->tipo_id)
                                          <option value="{{$tipo->id}}"
                                                  selected>{{$tipo->nombre}}</option>
                                      @else
                                          <option
                                              value="{{$tipo->id}}">{{$tipo->nombre}}</option>
                                      @endif
                                  @endforeach
                              </select>
                            </div>

                            <div class="col-md-2">
                              <label>Estado</label>
                              <select class="form-control" name="estado" class="form-control">
                                  <option value="">Selecciona..</option>
                                  @if ($usuario->estado == 1)
                                    <option value="1" selected>Activo</option>
                                    <option value="2">Inactivo</option>
                                  @else
                                    <option value="1">Activo</option>
                                    <option value="2" selected>Inactivo</option>
                                  @endif
                              </select>
                            </div>

                            <div class="col-md-2">

                                <button style="margin-bottom: 8px"
                                        class="btn-block btn btn-outline-primary" type="submit">
                                    Guardar <i class="mdi mdi-content-save"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
