@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <form action="{{route('usuarios.save')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="usuario" value="{{$usuario->id}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <center><h4>EDITAR USUARIO</h4></center>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-2">
                              <label>Nombre</label>
                              <input type="text" name="name" value="{{$usuario->name}}" class="form-control">
                            </div>
                            <div class="col-md-2">
                              <label>Email</label>
                              <input type="text" name="email" value="{{$usuario->email}}" class="form-control">
                            </div>
                            <div class="col-md-2">
                              <label>Contraseña</label>
                              <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-2">
                              <label>Tipo</label>
                              <select class="form-control" name="tipo" class="form-control">
                                  <option value="">Selecciona..</option>
                                  @if ($usuario->puesto == 'admin')
                                    <option value="admin" selected>Administrador</option>
                                    <option value="analista">Analista</option>
                                    <option value="consultas">Consultas</option>
                                  @elseif ($usuario->puesto == 'analista')
                                    <option value="admin">Administrador</option>
                                    <option value="analista" selected>Analista</option>
                                    <option value="consultas">Consultas</option>
                                  @elseif ($usuario->puesto == 'consultas')
                                    <option value="admin">Administrador</option>
                                    <option value="analista">Analista</option>
                                    <option value="consultas" selected>Consultas</option>
                                  @else
                                    <option value="admin">Administrador</option>
                                    <option value="analista">Analista</option>
                                    <option value="consultas">Consultas</option>
                                  @endif
                              </select>
                            </div>

                            <div class="col-md-2">
                              <label>Estado</label>
                              <select class="form-control" name="estado" class="form-control">
                                  <option value="">Selecciona..</option>
                                  @if ($usuario->estado == 'A')
                                    <option value="A" selected>Activo</option>
                                    <option value="I">Inactivo</option>
                                  @else
                                    <option value="A">Activo</option>
                                    <option value="I" selected>Inactivo</option>
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
