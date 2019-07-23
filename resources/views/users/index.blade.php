@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h4>Lista de Usuarios Web</h4>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-9">
                            <form class="form-inline" action="{{route('usuarios')}}"
                                  method="get">
                              <label class="sr-only">NOMBRE</label>
                              <input type="text" class="form-control mb-2 mr-sm-2" name="nombre_filtro" placeholder="NOMBRE"
                                     value="{{$nombre_filtro}}">

                                <button class="btn btn-success mb-2" type="submit">BUSCAR</button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <a href="{{route('usuarios.new')}}" class="btn btn-success mb-2">NUEVO USUARIO</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table style="width: 100%;text-align:center;font-size: 12px;" class="table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%;">Nombre</th>
                                        <th style="width: 15%;">E-mail</th>
                                        <th style="width: 10%;">Tipo</th>
                                        <th style="width: 10%;">Estado</th>
                                        <th style="width: 10%;">Accion</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($usuarios as $usuario)
                                        <tr>
                                            <td>{{ $usuario->name }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>{{ $usuario->puesto }}</td>
                                            <td>{{ $usuario->estado }}</td>
                                            <td>
                                                <form action="{{route('usuarios.view', ['usuario' => $usuario->id])}}">
                                                    <button style="margin-bottom: 8px"
                                                            class="btn-sm btn btn-outline-primary">
                                                        Ver <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                </form>
                                                <form action="{{route('usuarios.delete', ['usuario' => $usuario->id])}}">
                                                    <button style="margin-bottom: 8px"
                                                            class="btn-sm btn btn-outline-danger">
                                                        Eliminar <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <br>
                                {{ $usuarios->appends([])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
