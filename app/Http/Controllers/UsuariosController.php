<?php

namespace App\Http\Controllers;

use App\AdminTable as Users;
use App\Usuarios;
use App\TipoUsuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;


class UsuariosController extends Controller
{
    public function index(){
        $nombre_filtro = '';

        $perPage = 150;
        $page = Input::get('page');
        $pageName = 'page';
        $page = Paginator::resolveCurrentPage($pageName);
        $offSet = ($page * $perPage) - $perPage;

        $model = Users::where('id', '>', 1)->where('estado', '!=', 'E');
        $modelAux1 = Users::where('id', '>', 1)->where('estado', '!=', 'E');
        $modelAux2 = Users::where('id', '>', 1)->where('estado', '!=', 'E');

        if(Input::has('nombre_filtro')){
            $nombre_filtro = Input::get('nombre_filtro');
            if($nombre_filtro != ''){
                $model = $model->where('name', 'like', DB::raw("'%$nombre_filtro%'"));
                $modelAux1 = $modelAux1->where('name', 'like', DB::raw("'%$nombre_filtro%'"));
                $modelAux2 = $modelAux2->where('name', 'like', DB::raw("'%$nombre_filtro%'"));
            }
        }
        $modelAux = $model;
        $total_registros = $modelAux->count();
        $model = $model->offset($offSet)->limit($perPage)->orderBy('id')->get();

        $posts = new LengthAwarePaginator($model, $total_registros, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return view('users.index', [
            'usuarios' => $posts,
            'nombre_filtro' => $nombre_filtro
        ]);
    }

    public function new(){
      $usuario = new Users();
      $usuario->id = 0;
      return view('users.view', [
          'usuario' => $usuario
      ]);
    }

    public function view($usuario){
      $usuario = Users::find($usuario);
      return view('users.view', [
          'usuario' => $usuario
      ]);
    }

    public function save(Request $request){
      if($request->usuario == 0){
        $usuario = new Users();

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        if($request->password != ''){
          $usuario->password = bcrypt($request->password);
        }
        if($request->tipo == 'admin' || $request->tipo == 'analista'){
          $usuario->tipo_usuario = 1;
        }elseif($request->tipo == 'consultas'){
          $usuario->tipo_usuario = 2;
        }
        $usuario->puesto = $request->tipo;
        $usuario->estado = $request->estado;
        $usuario->save();
      }else{
        $usuario = Users::find($request->usuario);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        if($request->password != ''){
          $usuario->password = bcrypt($request->password);
        }
        if($request->tipo == 'admin' || $request->tipo == 'analista'){
          $usuario->tipo_usuario = 1;
        }elseif($request->tipo == 'consultas'){
          $usuario->tipo_usuario = 2;
        }
        $usuario->puesto = $request->tipo;
        $usuario->estado = $request->estado;
        $usuario->save();
      }

      return redirect()->route('usuarios.view', ['usuario' => $usuario->id]);
    }

    public function delete($usuario){
      $usuario = Users::find($usuario);
      $usuario->estado = 'E';
      $usuario->save();
      return redirect()->route('usuarios');
    }

    //usuarios Terreno
    public function indexTe(){
        $nombre_filtro = '';

        $perPage = 150;
        $page = Input::get('page');
        $pageName = 'page';
        $page = Paginator::resolveCurrentPage($pageName);
        $offSet = ($page * $perPage) - $perPage;

        $model = Usuarios::where('id', '>', 0)->where('estado', '!=', 3);
        $modelAux1 = Usuarios::where('id', '>', 0)->where('estado', '!=', 3);
        $modelAux2 = Usuarios::where('id', '>', 0)->where('estado', '!=', 3);

        if(Input::has('nombre_filtro')){
            $nombre_filtro = Input::get('nombre_filtro');
            if($nombre_filtro != ''){
                $model = $model->where('nombre', 'like', DB::raw("'%$nombre_filtro%'"));
                $modelAux1 = $modelAux1->where('nombre', 'like', DB::raw("'%$nombre_filtro%'"));
                $modelAux2 = $modelAux2->where('nombre', 'like', DB::raw("'%$nombre_filtro%'"));
            }
        }
        $modelAux = $model;
        $total_registros = $modelAux->count();
        $model = $model->offset($offSet)->limit($perPage)->orderBy('id')->get();

        $posts = new LengthAwarePaginator($model, $total_registros, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return view('userste.index', [
            'usuarios' => $posts,
            'nombre_filtro' => $nombre_filtro
        ]);
    }

    public function newTe(){
      $usuario = new Usuarios();
      $usuario->id = 0;
      return view('userste.view', [
          'usuario' => $usuario,
          'tipos' => TipoUsuario::all(),
      ]);
    }

    public function viewTe($usuario){
      $usuario = Usuarios::find($usuario);
      return view('userste.view', [
          'usuario' => $usuario,
          'tipos' => TipoUsuario::all(),
      ]);
    }

    public function saveTe(Request $request){
      if($request->usuario == 0){
        $usuario = new Usuarios();

        $usuario->nombre = $request->nombre;
        $usuario->nickname = $request->nickname;
        if($request->password != ''){
          $usuario->contrasena = $request->password;
        }
        $usuario->tipo_id = $request->tipo;
        $usuario->labor_id = 1;
        $usuario->estado = $request->estado;
        $usuario->save();
      }else{
        $usuario = Usuarios::find($request->usuario);

        $usuario->nombre = $request->nombre;
        $usuario->nickname = $request->nickname;
        if($request->password != ''){
          $usuario->contrasena = $request->password;
        }
        $usuario->tipo_id = $request->tipo;
        $usuario->labor_id = 1;
        $usuario->estado = $request->estado;
        $usuario->save();
      }

      return redirect()->route('usuarioste.view', ['usuario' => $usuario->id]);
    }

    public function deleteTe($usuario){
      $usuario = Usuarios::find($usuario);
      $usuario->estado = 3;
      $usuario->save();
      return redirect()->route('usuarioste');
    }
}
