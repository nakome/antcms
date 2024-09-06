<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\Session\Session as Session;

/**
 * Trait MessageHandler - Manejo de mensajes
 * -----------------------------
 * msgGet: funcion para obtener un mensaje
 * msgSet: funcion para establecer un mensaje para ser mostrado en la página
 * --------------------------------
 */
trait MessageHandlerTrait
{

    /**
     * Función para obtener un mensaje.
     *
     * @param string $callback El callback para obtener el mensaje
     *
     * @return callback
     */
    public function msgGet()
    {
        // Verificamos si hay un mensaje almacenado en la sesión
        if (Session::get('msg')) {

            $msg = Session::get('msg'); // Obtenemos el mensaje
            Session::delete('msg'); // Borramos el mensaje de la sesión

        }
        // Si existe un mensaje almacenado, lo mostramos en una ventana emergente
        if (isset($msg)) {
            return '<script type="text/javascript" nonce="'.NONCE.'">message("' . $msg['title'] . '","' . $msg['msg'] . '");</script>';
        }
    }

    /**
     * Establece un mensaje para ser mostrado en la página.
     *
     * @param string $title El título del mensaje.
     * @param string $msg   El contenido del mensaje.
     */
    public function msgSet(string $title = "", string $msg = "")
    {
        // Creamos un array con los datos del mensaje
        $data = array(
            'title' => $title,
            'msg' => $msg,
        );
        // Almacenamos el mensaje en la sesión para que sea visible en la próxima página
        Session::set('msg', $data);
    }
}