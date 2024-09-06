<?php

declare (strict_types = 1);

namespace Vendor\Url;

final class Url
{
    /**
     * Obtener url base
     *
     * Esta función devuelve la URL base de la aplicación. Si la aplicación se está ejecutando en localhost,
     * la URL base se tomará del archivo de configuración. Si la aplicación se está ejecutando en un servidor
     * remoto, la URL base se construirá a partir de la información del servidor.
     *
     * @return string url
     */
    public static function base(): string
    {
        // lista blanca de direcciones IP para localhost
        $whitelist = [
            '127.0.0.1',
            '::1',
        ];

        // Comprobar si la aplicación se está ejecutando en localhost
        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            // construir la URL base usando información del servidor
            $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
            return $https . rtrim(rtrim($_SERVER['HTTP_HOST'], '\\/') . dirname($_SERVER['PHP_SELF']), '\\/');
        } else {
            // obtener la URL base desde la configuración
            return SITE_URL;
        }
    }

    /**
     * Obtener url actual
     *
     * <code>
     *  Url::current();
     * </code>
     *
     * @return string
     */
    public static function current(): string
    {
        $url         = ''; // Inicializa la variable $url como una cadena vacía
        $request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''; // Obtiene la URI de la solicitud, o vacío si no se establece
        $script_url  = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : ''; // Obtiene la URL del script actual, o vacío si no se establece

        // Si la URI de la solicitud y la URL del script actual no son iguales
        if ($request_url != $script_url) {
            // Remueve el nombre del script actual de la URI de la solicitud
            $url = trim(preg_replace('/' . str_replace('/', '\\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
        }

        // Remueve cualquier cadena de consulta en la URL
        $url = preg_replace('/\\?.*/', '', $url);

        return $url; // Devuelve la URL actual
    }

    /**
     * Segmentos url
     *
     * <code>
     *  Url::segments();
     * </code>
     *
     * @return array
     */
    public static function segments(): array
    {
        // Primero, obtenemos la URL actual a través de la función current().
        // Esta función elimina la parte de la URL que corresponde al script PHP y devuelve el resto de la URL.
        $url = self::current();

        // Luego, dividimos la URL en segmentos usando la función explode().
        // La función explode() toma dos argumentos: el delimitador y la cadena que se dividirá en segmentos.
        // En este caso, usamos el delimitador '/' para dividir la URL en segmentos.
        // La función explode() devuelve un array de segmentos.
        return explode('/', $url);
    }

    /**
     * Segmento url
     *
     * <code>
     *  Url::segment(1);
     * </code>
     *
     * @param int $num El número del segmento que se desea obtener. 0 devuelve el primer segmento.
     *
     * @return string El segmento de la URL correspondiente al número proporcionado. Si el segmento no existe, devuelve una cadena vacía.
     */
    public static function segment(int $num = 0): string
    {
        // Obtener todos los segmentos de la URL actual
        $segments = self::segments();
        // Devolver el segmento que se corresponde con el número proporcionado, si existe.
        return isset($segments[$num]) ? $segments[$num] : '';
    }

    /**
     * Sanitiza una URL dada eliminando caracteres especiales y recortando partes innecesarias.
     *
     * @param string $url La URL a ser sanitizada.
     * @return string La URL sanitizada.
     */
    public static function sanitize(string $url): string
    {
        $url = trim($url);
        $url = rawurldecode($url);

        // Define los caracteres especiales a reemplazar
        $special_chars = ['--' => '-', '&quot;' => '-', '!' => '', '@' => '', '#' => '', '$' => '', '%' => '', '^' => '', '*' => '', '(' => '', ')' => '', '+' => '', '{' => '', '}' => '', '|' => '', ':' => '', '"' => '', '<' => '', '>' => '', '[' => '', ']' => '', '\\' => '', ';' => '', "'" => '', ',' => '', '*' => '', '+' => '', '~' => '', '`' => '', 'laquo' => '', 'raquo' => '', ']>' => '', '&#8216;' => '', '&#8217;' => '', '&#8220;' => '', '&#8221;' => '', '&#8211;' => '', '&#8212;' => ''];

        // Realizar la sustitución de caracteres especiales
        $url = strtr($url, $special_chars);

        $url = str_replace('--', '-', $url);
        $url = rtrim($url, '-');
        $url = str_replace('..', '', $url);
        $url = str_replace('//', '', $url);
        $url = preg_replace('/^\//', '', $url);
        $url = preg_replace('/^\./', '', $url);

        return $url;
    }

    /**
     * Reemplaza la ruta de contenido con la URL del sitio y elimina index.html, .html y barras diagonales finales de la URL de la página.
     *
     * @param string $site_url La URL base del sitio.
     * @param string $page La URL de la página a analizar.
     * @return string La URL de la página analizada.
     */
    public static function parse(string $site_url, string $page): string
    {
        return preg_replace(
            [
                '/^' . preg_quote(CONTENT, '/') . '/',
                '/index.html$/',
                '/\.html$/',
                '/\\\\+/',
                '/\/$/',
            ],
            [
                $site_url,
                '',
                '',
                '/',
                '',
            ],
            $page
        );
    }
}
