<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\PasswordHasher\PasswordHasher as PasswordHasher;
use Vendor\Session\Session as Session;
use function base64_encode;



/**
 * Trait Authentication - Autenticación
 * -------------------------------------
 * toManyAttempts: muestra una página de bloqueo temporal para intentos de acceso fallidos
 * login: realiza la autenticación del usuario
 * logout: cierra la sesión
 * -------------------------------------
 */
trait AuthenticationTrait
{

    /**
     * toManyAttempts
     *
     * @return string   Devuelve una cadena de texto con el código HTML de una página
     *                  que indica que se han realizado demasiados intentos de acceso
     *                  y se ha bloqueado temporalmente el acceso.
     */
    public function toManyAttempts(): string
    {
        // Código HTML de la página
        return '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Acceso bloqueado</title><link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>' . EMOJI_FAVICON . '</text></svg>"><style rel="stylesheet">*{box-sizing:border-box}body,html{position:relative;height:100%}body{margin:0;padding:0;background:#eee}main{display:flex;justify-content:center;align-items:center;height:100%}section{margin:5px;max-width:30rem;padding:10px 20px;width:100%;border-radius:4px;background:#fff;border:1px solid #ddd}section h1{font-size:28px;line-height:1.5;margin:0;margin-bottom:10px;color:#333}section p{margin:0;margin-bottom:10px;font-size:16px;line-height:1.5;color:#777}</style></head><body><main><section><h1>' . $this->lang('toManyAttempts') . '</h1><p>' . $this->lang('youHaveToWait') . ' <span id="num">5</span> ' . $this->lang('secondsToRetry') . '. </p></section><script rel="javascript">let id=document.getElementById("num"),count=5,i=setInterval(()=>{count-=1,id.textContent=count,0===count&&location.reload(!0)},1e3);</script></main></body></html>';
    }

    /**
     * Verifica si el usuario ha iniciado sesión.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        // Verificar si existen las claves necesarias en la sesión y si el hash de inicio de sesión coincide
        // ademas de comprobar si la cookie de usuario coincide
        return $this->__login_hash === Session::get('_login_hash') && Session::get('_ip') && Session::get('_time');

    }

    /**
     * login
     *
     * @return void
     */
    public function login(): void
    {
        // Verificar que no esté vacía la contraseña
        if (empty(PASSWORD_HASH)) {
            $this->error($this->lang('emptyPassword'));
            error_log($this->lang('emptyPassword'));
        }

        // Iniciamos la clase PasswordHasher
        $hasher = new PasswordHasher(PASSWORD_BCRYPT, ['cost' => 50]);

        // Obtener el número de intentos de acceso fallidos
        $intentos = Session::get('intentos_acceso');

        // Si hay 3 o más intentos, bloquear el acceso
        if ($intentos >= 3) {
            // Insertar una cookie de bloqueo de usuario durante 5 segundos
            setcookie('usuario_bloqueado', (string) true, time() + 5, "/", ADMIN_URL, true, true);
            // Reiniciar el contador de intentos de acceso
            Session::set('intentos_acceso', 0);
            // Redirigir al usuario a la página principal
            $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
        }

        // Comprobar si existe la cookie de bloqueo de usuario
        if (array_key_exists('usuario_bloqueado', $_COOKIE)) {
            error_log('toManyAttempts loaded');
            // Mostrar la plantilla de error de demasiados intentos
            die($this->toManyAttempts());
            // Salir del script
            exit(0);
        } else {

            $password = trim($this->post('password', true));
            // Comprobar si la contraseña es correcta
            if ($hasher->verify($password, PASSWORD_HASH)) {

                // Insertar las variables de sesión correspondientes
                Session::set('_login_hash', $this->__login_hash); // Insertar el hash de inicio de sesión
                Session::set('_ip', $this->__ip); // Guardar la dirección IP del usuario
                Session::set('_time', date('m-d-Y h:m:s')); // Guardar la fecha y hora de inicio de sesión
                Session::set('intentos_acceso', 0); // Reiniciar el contador de intentos de acceso

                // Redirigir al usuario a la página principal
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
            } else {
                // Incrementar el contador de intentos de acceso fallidos
                $count = $intentos + 1;

                // Insertar el nuevo valor del contador en la sesión
                Session::set('intentos_acceso', $count);

                // Mostrar un mensaje de error y redirigir al usuario a la página principal
                $this->msgSet($this->lang('error'), $this->lang('invalidPassword') . ' ' . (abs($count - 3)) . ' ' . $this->lang('attemps'));

                error_log($this->lang('invalidPassword'));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
            }
        }
    }

    /**
     * logout
     * Esta función se encarga de cerrar sesión del usuario, eliminando todas las variables de sesión y redirigiendo al sitio principal.
     *
     * @return void
     */
    public function logout(): void
    {
        // Verificamos si la sesión está iniciada
        $sessionStarted = Session::start();
        if ($sessionStarted) {

            // Eliminamos las variables de sesión correspondientes
            Session::delete('_login_hash');
            Session::delete('_uid');
            Session::delete('_ip');
            Session::delete('_time');
            Session::destroy();

            error_log($this->lang('logout'));

            // Redirigimos al sitio principal
            $this->redirect(ADMIN_URL);
        }
    }
}
