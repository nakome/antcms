<?php

declare (strict_types = 1);

namespace Vendor\Session;

/**
 * Class Session para el manejo de sesiones
 * -------------------------------------
 * session::start: funcion que inicia la sesion
 * session::delete: funcion que elimina un valor de la sesion
 * session::destroy: funcion que destruye la sesion
 * session::exists: funcion que verifica si existen todas las claves
 * session::set: funcion que establece un valor en la sesion
 * session::get: funcion que obtiene un valor de la sesion
 * -------------------------------------
 */
final class Session
{
    /**
     * Iniciar sesión.
     *
     * Este método verifica si la sesión ya ha sido iniciada y la inicia si aún no lo ha sido.
     *
     * @return bool - Devuelve true si la sesión ya estaba iniciada o si se inició correctamente, o false si no se pudo iniciar la sesión.
     */
    public static function start(): bool
    {
        // Si la sesión ya se inició, devolver true; de lo contrario, iniciar la sesión y devolver el resultado
        return session_id() || @session_start();
    }

    /**
     * Elimina uno o varios valores de la sesión.
     *
     * @param mixed ...$args  Uno o varios valores de la sesión a eliminar.
     *                        Pueden ser especificados como argumentos separados o como un arreglo.
     *                        Cada valor debe ser una clave válida de la sesión.
     * @return void
     */
    public static function delete(...$args): void
    {
        // Si el primer argumento es un array, recorrerlo y eliminar cada clave
        if (is_array($args[0])) {
            foreach ($args[0] as $key) {
                unset($_SESSION[$key]);
            }
        } else {
            // Si el primer argumento no es un array, eliminar cada argumento individual
            foreach ($args as $key) {
                unset($_SESSION[$key]);
            }
        }
    }

    /**
     * Destruye la sesión actual y elimina todas las variables de sesión.
     *
     * @return void
     */
    public static function destroy(): void
    {
        // Iniciar la sesión si no se ha iniciado ya
        if (!session_id()) {
            session_start();
        }

        // Eliminar todas las variables de sesión
        $_SESSION = [];

        // Destruir la sesión
        session_destroy();

        // Asegurarse de que la sesión se haya destruido correctamente
        if (session_id()) {
            // Forzar la eliminación de la sesión
            session_write_close();
        }
    }

    /**
     * Verifica si existen todas las claves proporcionadas en la sesión.
     *
     * @param string ...$keys Una lista de claves a verificar en la sesión.
     * @return bool True si todas las claves existen en la sesión, False en caso contrario.
     */
    public static function exists(string ...$keys): bool
    {
        // Iniciar la sesión si es necesario
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        // Verificar si todas las claves existen en la sesión
        $allKeysExist = array_reduce($keys, function ($exists, $key) {
            return $exists && isset($_SESSION[$key]);
        }, true);

        return $allKeysExist;
    }

    /**
     * Establecer sesión.
     *
     * @param  string $key   clave
     * @param  mixed  $value valor
     */
    public static function set(string $key, $value): void
    {
        // Iniciar sesión si es necesario
        if (!session_id()) {
            self::start();
        }

        // Verificar que la clave no sea una cadena vacía
        if ($key !== '') {
            // Establecer la clave y valor en la sesión
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Obtener sesión.
     *
     * @param string $key la clave de la sesión a obtener
     * @return mixed el valor de la clave de la sesión o null si la clave no existe
     */
    public static function get($key)
    {
        // Iniciar sesión si es necesario
        self::start();
        // Obtener la clave
        return $_SESSION[$key] ?? null;
    }
}
