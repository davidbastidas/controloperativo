<?php

namespace App\Http\Controllers;

use App\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class DownloadController extends Controller
{
    public $avisos = null;


    public function download(Request $request)
    {
        $agenda = $request->agenda;

        $model = new Auditoria();
        $avisos = $model->hydrate(
            DB::select(
                "call download_auditorias($agenda)"
            )
        );

        $this->avisos = $avisos;

        Excel::create('Auditorias', function ($excel) {

            $avisos = $this->avisos;

            $excel->sheet('Auditorias', function ($sheet) use ($avisos) {

                $sheet->fromArray($avisos);

            });

        })->export('xlsx');
    }
}
