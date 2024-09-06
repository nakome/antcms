<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait RequestHandler - Manejo de solicitudes
 * -----------------------------
 * post: Función para obtener un POST
 * get: Función para obtener un GET
 * ------------------------------
 */
trait RequestHandlerTrait
{
    /**
     * $_POST
     *
     * @param string $key
     * @return string
     */
    public function post(string $key, bool $sanitize = true): string
    {
        //comprobar si una cadena de texto contiene sólo caracteres alfanuméricos (letras y números).
        if ($sanitize && !ctype_alnum($key) || empty($key)) {
            return "";
        }
        // Validar y filtrar $_POST[$key]
        $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);

        // Decodificar el valor de la variable si es necesario
        $value = $value ? urldecode($value) : "";

        // Codificar los caracteres especiales en entidades HTML
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        // Eliminar los espacios en blanco al inicio y al final del valor
        $value = trim($value);

        // Comprobamos si $sanitize es true y sino lo pasamos normal
        // Válido para cuando queramos editar archivos sin perder datos
        return ($sanitize) ? $value : (isset($_POST[$key]) ? $_POST[$key] : "");
    }

    /**
     * $_GET
     *
     * @param string $key
     * @return string
     */
    public function get(string $key): string
    {
        // Verificar si $key es válido
        if (!ctype_alnum($key) || empty($key)) {
            return "";
        }
        // Validar y filtrar $_GET[$key]
        $value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK | FILTER_FLAG_NO_ENCODE_QUOTES);

        // Decodificar el valor de la variable si es necesario
        $value = urldecode($value);

        // Codificar los caracteres especiales en entidades HTML
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        // Eliminar los espacios en blanco al inicio y al final del valor
        $value = trim($value);

        return $value;
    }

}