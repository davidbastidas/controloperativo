<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/agenda/upload',
        'admin/agenda/download',
        'admin/agenda/asignar',
        'admin/agenda/asignarall',
        'admin/agenda/vaciar-carga',
        'admin/agenda/save',
        'admin/agenda/servicio/save',
        'admin/agenda/servicio/delete/all',
        'admin/dashboard/getAvancePorGestor',
        'admin/dashboard/getAvanceDiario',
        'admin/dashboard/getPointMapGestores',
        'admin/servicios/getPointMapVisita',
    ];
}
