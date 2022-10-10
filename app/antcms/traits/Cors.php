<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Cors
{

    /**
     * C.O.R.S function.
     *
     * <code>
     *      AntCMS::cors();
     * </code>
     *
     * @return <type>
     */
    public static function cors(): void
    {
        // Permitir desde cualquier origen
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide si el origen en $_SERVER['HTTP_ORIGIN'] es uno
            // desea permitir, y si es así:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // cache 1 dia
        }
        // Los encabezados de control de acceso se reciben durante las solicitudes de OPCIONES
        if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }

}
