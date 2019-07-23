<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', [
        'as' => '/',
        'uses' => 'HomeController@index'
    ]);

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('admin/agenda', [
        'as' => 'agenda',
        'uses' => 'AgendaController@index'
    ]);

    Route::get('admin/agenda/viewsubiragenda/{agenda}', [
        'as' => 'agenda.subir',
        'uses' => 'AgendaController@viewUpload'
    ]);

    Route::post('admin/agenda/upload', [
        'as' => 'agenda.upload',
        'uses' => 'AgendaController@subirServicios'
    ]);

    Route::post('admin/agenda/download', [
        'as' => 'agenda.download',
        'uses' => 'DownloadController@download'
    ]);

    Route::get('admin/agenda/{agenda}', 'AgendaController@listar')->name('agenda.detalle');

    Route::post('admin/agenda/asignar', [
        'as' => 'agenda.asignar',
        'uses' => 'AgendaController@asignarUnoAUno'
    ]);

    Route::post('admin/agenda/asignarall', [
        'as' => 'agenda.asignarall',
        'uses' => 'AgendaController@asignarAll'
    ]);

    Route::post('admin/agenda/vaciar-carga', [
        'as' => 'agenda.vaciarcarga',
        'uses' => 'AgendaController@vaciarCarga'
    ]);
    Route::post('admin/agenda/save', [
        'as' => 'agenda.save',
        'uses' => 'AgendaController@saveAgenda'
    ]);

    Route::get('admin/agenda/delete/{agenda}', [
        'as' => 'agenda.delete',
        'uses' => 'AgendaController@deleteAgenda'
    ]);

    Route::get('admin/agenda/servicio/editar/{agenda}/{servicio}', [
        'as' => 'servicio.editar',
        'uses' => 'AgendaController@viewServicio'
    ]);

    Route::post('admin/agenda/servicio/save', [
        'as' => 'servicio.update',
        'uses' => 'AgendaController@saveAviso'
    ]);

    Route::get('admin/agenda/servicio/delete/{agenda}/{servicio}', [
        'as' => 'servicio.eliminar',
        'uses' => 'AgendaController@deleteServicio'
    ]);

    Route::post('admin/agenda/servicio/delete/all', [
        'as' => 'servicio.eliminar.all',
        'uses' => 'AgendaController@deleteServicioPorSeleccion'
    ]);

    Route::get('admin/mapas', [
        'as' => 'mapas',
        'uses' => 'AgendaController@visitaMapa'
    ]);

    Route::match(['get', 'post'], '/admin',
        [
            'as' => 'admin',
            'uses' => 'AdminController@index'
        ]
    );

    Route::get('admin/dashboard/{id}', [
        'as' => 'admin.dashboard',
        'uses' => 'AdminController@dashboard'
    ]);

    Route::match(['get', 'post'], 'admin/img-panel',
        [
            'as' => 'admin.img',
            'uses' => 'ImagesController@index'
        ]
    );

    Route::post('admin/dashboard/getAvancePorGestor', [
        'as' => 'admin.dashboard.getAvancePorGestor',
        'uses' => 'DashboardController@getAvancePorGestor'
    ]);

    Route::post('admin/dashboard/getAvanceDiario', [
        'as' => 'admin.dashboard.getAvanceDiario',
        'uses' => 'DashboardController@getAvanceDiario'
    ]);

    Route::post('admin/dashboard/getPointMapGestores', [
        'as' => 'admin.dashboard.getPointMapGestores',
        'uses' => 'DashboardController@getPointMapGestores'
    ]);

    Route::post('admin/servicios/getPointMapVisita', [
        'as' => 'admin.servicios.getPointMapVisita',
        'uses' => 'AgendaController@getPointMapVisita'
    ]);

    //carga de lecturas Pci
    Route::match(['get', 'post'], '/admin/pci/lecturas',
        [
            'as' => 'agenda.pci.uploadlecturas',
            'uses' => 'AgendaController@subirLecturasPci'
        ]
    );

    //consultas de servicios
    Route::match(['get', 'post'], '/admin/consultas/servicios',
        [
            'as' => 'agenda.consultas.servicios',
            'uses' => 'AgendaController@consultaServicios'
        ]
    );

    //admin Usuarios
    Route::get('admin/usuarios', [
        'as' => 'usuarios',
        'uses' => 'UsuariosController@index'
    ]);

    Route::get('admin/usuarios/view/{usuario}', [
        'as' => 'usuarios.view',
        'uses' => 'UsuariosController@view'
    ]);

    Route::get('admin/usuarios/new', [
        'as' => 'usuarios.new',
        'uses' => 'UsuariosController@new'
    ]);

    Route::post('admin/usuarios/save', [
        'as' => 'usuarios.save',
        'uses' => 'UsuariosController@save'
    ]);

    Route::get('admin/usuarios/delete/{usuario}', [
        'as' => 'usuarios.delete',
        'uses' => 'UsuariosController@delete'
    ]);

    //admin Usuarios Terreno
    Route::get('admin/usuarioste', [
        'as' => 'usuarioste',
        'uses' => 'UsuariosController@indexTe'
    ]);

    Route::get('admin/usuarioste/view/{usuario}', [
        'as' => 'usuarioste.view',
        'uses' => 'UsuariosController@viewTe'
    ]);

    Route::get('admin/usuarioste/new', [
        'as' => 'usuarioste.new',
        'uses' => 'UsuariosController@newTe'
    ]);

    Route::post('admin/usuarioste/save', [
        'as' => 'usuarioste.save',
        'uses' => 'UsuariosController@saveTe'
    ]);

    Route::get('admin/usuarioste/delete/{usuario}', [
        'as' => 'usuarioste.delete',
        'uses' => 'UsuariosController@deleteTe'
    ]);
});
