<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\Session\Session as Session;

/**
 * Trait TokenManager - Generar Token
 * -------------------------------------
 * generateToken: funcion que genera un token aleatorio
 * checkToken: funcion que verifica si un token enviado en una solicitud coincide con el que se guardó previamente en la sesión
 * captchaToken: funcion que genera un token de captcha
 * -------------------------------------
 */
trait TokenManagerTrait
{

    /**
     * Generar Token
     *
     * Este método genera un token aleatorio seguro para su uso en varias aplicaciones, como la autenticación y la verificación de formularios.
     * El token se devuelve para su posterior uso.
     *
     * @param int $length (opcional) La longitud del token generado (por defecto 32)
     * @return string $token - El token generado
     */
    public function generateToken($length = 32): string
    {
        // Verificar si la sesión ha sido iniciada
        if (Session::start()) {
            // Generar un identificador único seguro
            $uniqId = random_bytes(16);
            // Aplicar la función hash SHA-256 al identificador único
            $sha256 = hash('sha256', $uniqId);
            // Convertir el resultado de la función hash a base 36
            $baseConvert = base_convert($sha256, 16, 36);
            // Tomar los primeros caracteres del resultado de la conversión
            $token = substr($baseConvert, 0, $length);
            // Guardar el token en la sesión
            $_SESSION['token'] = $token;
            // Devolver el token generado
            return $token;
        }
    }

    /**
     * Check token
     *
     * Este método verifica si un token enviado en una solicitud coincide con el que se guardó previamente en la sesión del usuario.
     * Se utiliza para prevenir ataques CSRF (Cross-site request forgery) y proteger la integridad de los datos del usuario.
     *
     * @param string $token - El token enviado en la solicitud
     * @return bool - Devuelve verdadero si el token coincide con el de la sesión, falso en caso contrario
     */
    public function checkToken(string $token = ""): bool
    {
        // Comprobar si el token es nulo
        if ($token === null) {
            return false;
        }

        // Comparar el token enviado con el de la sesión del usuario
        return $token === Session::get('token');
    }

    /**
     * Generar un código de captcha aleatorio.
     *
     * @param int $length la longitud del código, por defecto es 6
     * @param string $characters los caracteres permitidos para el código, por defecto son las letras mayúsculas del alfabeto inglés y los números del 0 al 9
     * @return string el código de captcha generado
     */
    public function captchaToken(int $length = 6, string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'): string
    {
        $randomString = '';
        $maxIndex = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $maxIndex)];
        }
        return $randomString;
    }
}