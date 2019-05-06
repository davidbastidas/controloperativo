@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h4>Consulta de Servicios</h4>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-inline" action="{{route('agenda.consultas.servicios')}}"
                                  method="get">
                                <label class="sr-only">NIC</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" name="nic_filtro" placeholder="NIC"
                                       value="{{$nic_filtro}}">


                                <label class="sr-only">MEDIDOR</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" name="medidor_filtro"
                                       placeholder="MEDIDOR" value="{{$medidor_filtro}}">

                                <button class="btn btn-success mb-2" type="submit">Consultar</button>
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
                                          #
                                        </th>
                                        <th style="width: 10%;">FECHA</th>
                                        <th style="width: 20%;">NIC/CT-MT</th>
                                        <th style="width: 15%;">MEDIDOR</th>
                                        <th style="width: 10%;">LECTURA</th>
                                        <th style="width: 15%;">ANOMALIA</th>
                                        <th style="width: 10%;">LECTOR</th>
                                        <th style="width: 10%;">FOTO</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($servicios as $serv)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $serv->fecha }}</td>
                                            <td>{{ $serv->nicct }}</td>
                                            <td>{{ $serv->medidor }}</td>
                                            <td>{{ $serv->lectura }}</td>
                                            <td>{{ $serv->anomalia }}</td>
                                            <td>{{ $serv->lector }}</td>
                                            <td><a href="{{ $serv->path }}" download>Foto</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
