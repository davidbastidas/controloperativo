@extends('layouts.app')
<style>
    .little{
        font-size: 20px !important;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <center><h3>SUBIR LECTURAS DE PCI</h3></center>
                    <br><br>
                    <div class="row">
                        <div class="col-md-12" style="top: -20px;">
                            Por favor, Ten en cuenta la fecha de carga, el sistema toma la ultima lectura por fecha.
                        </div>
                    </div>
                    @if(isset($success))
                        <div class="alert alert-success" role="alert">
                            <strong>{{$success}}</strong>
                        </div>
                    @endif
                    <br>
                    <form method="post" action="{{route('agenda.pci.uploadlecturas')}}"
                          enctype="multipart/form-data" style="padding: 0">
                          {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-7">
                                <input class="form-control" type="file" name="file"/>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary" type="submit">Subir</button>
                            </div>
                        </div>
                    </form>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
@endsection
