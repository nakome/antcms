<?php

declare (strict_types = 1);

namespace Vendor\Template;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\Sanitize\Sanitize as Sanitize;

/**
 * Template.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 1.0.0
 */
class Template
{

    public $tags = [];
    public $tmp = [];
    public $data = [];
    /**
     * Constructor.
     */
    public function __construct()
    {
        // tags
        $this->tags = [];

        $this->tmp = ROOT . '/tmp/';
        if (!file_exists($this->tmp)) {
            mkdir($this->tmp);
        }

        $this->removeCacheOneDay();
    }

    /**
     * Remove cache 1 day
     *
     * @return void
     */
    public function removeCacheOneDay(): void
    {
        // Se establece la zona horaria a utilizar
        date_default_timezone_set('Europe/Madrid');

        // Directorio donde se encuentra la caché
        $cache_dir = $this->tmp;

        // Se obtiene la hora actual en formato UNIX timestamp
        $now = time();

        // Se lee el archivo JSON que contiene la fecha de la última limpieza de caché
        $json = file_exists($cache_dir . '/cache_info.json') ? json_decode(file_get_contents($cache_dir . '/cache_info.json'), true) : [];

        // Se obtiene la fecha de la última limpieza de caché, que se almacena en el campo 'date' del archivo JSON
        $last_cleanup = (array_key_exists('date', $json)) ? (int) $json['date'] : 0;

        // Se comprueba si ya ha pasado un día desde la última limpieza de caché
        if ($now - $last_cleanup > 86400) {
            // Se eliminan los archivos HTML almacenados en la caché
            $files = glob($cache_dir . '/*.html');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Se actualiza el archivo JSON con la fecha y hora actual, y la fecha de limpieza
            file_put_contents($cache_dir . '/cache_info.json', json_encode([
                'date' => $now,
                'cleanup' => date("Y-m-d h:m:s", strtotime(date("d-m-Y"))),
            ]));
        }
    }

    /**
     * Callback.
     *
     * @param mixed $variable the var
     *
     * @return array|string
     */
    public function callback($variable)
    {
        if (!is_string($variable) && is_callable($variable)) {
            return $variable();
        }

        return $variable;
    }

    /**
     *  Set var.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return mixed
     */
    public function set(string $name, $value): object
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Append data in array.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return null
     */
    public function append($name, $value)
    {
        $this->data[$name][] = $value;
    }

    /**
     * Parse content.
     *
     * @param string $content el contenido a analizar y procesar
     *
     * @return string el contenido procesado
     */
    private function __parse(string $content): string
    {
        // reemplaza etiquetas con PHP
        foreach ($this->tags as $regexp => $replace) {
            if (false !== strpos($replace, 'self')) {
                $content = preg_replace_callback('#' . $regexp . '#s', $replace, $content);
            } else {
                $content = preg_replace('#' . $regexp . '#', $replace, $content);
            }
        }

        // reemplaza variables
        if (preg_match_all('/(\$(?:[a-zA-Z0-9_-]+)(?:\.(?:(?:[a-zA-Z0-9_-][^\s]+)))*)/', $content, $matches)) {
            for ($i = 0; $i < count($matches[1]); ++$i) {
                // $a.b to $a["b"]
                $rep = $this->__replaceVariable($matches[1][$i]);
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }

        // elimina espacios entre %% y $
        $content = preg_replace('/\%\%\s+/', '%%', $content);

        // llama a cv() para las variables firmadas
        if (preg_match_all('/\%\%(.)([a-zA-Z0-9_-]+)/', $content, $matches)) {
            for ($i = 0; $i < count($matches[2]); ++$i) {
                if ('$' == $matches[1][$i]) {
                    $content = str_replace($matches[0][$i], 'self::callback($' . $matches[2][$i] . ')', $content);
                } else {
                    $content = str_replace($matches[0][$i], $matches[1][$i] . $matches[2][$i], $content);
                }
            }
        }
        return $content;
    }

    /**
     * Ejecuta un archivo de plantilla.
     *
     * @param string $file    La ruta del archivo de plantilla a ejecutar.
     * @param int    $counter Un contador interno para evitar la recursión infinita.
     *
     * @return string El contenido de la plantilla procesada.
     */
    private function __run(string $file, int $counter = 0): string
    {
        // Extrae información de la ruta del archivo de plantilla.
        $pathInfo = pathinfo($file);
        // Genera un nombre de archivo temporal basado en la información de la ruta del archivo original.
        $tmpFile = $this->tmp . $pathInfo['basename'];

        // Verifica si el archivo existe.
        if (!is_file($file)) {
            // Si el archivo no existe, muestra un mensaje de error.
            echo "Plantilla '$file' no encontrada.";
        } else {
            // Lee el contenido del archivo.
            $content = Sanitize::file($file);

            // Verifica si el contenido de la plantilla contiene etiquetas que requieren ser procesadas.
            // Si es así, escribe el contenido en un archivo temporal y lo procesa nuevamente.
            if ($this->__searchTags($content) && ($counter < 3)) {
                file_put_contents($tmpFile, $content);
                $content = $this->__run($tmpFile, ++$counter);
            }

            // Procesa las etiquetas y variables de la plantilla.
            file_put_contents($tmpFile, $this->__parse($content));

            // Extrae las variables de la plantilla y las convierte en variables de PHP.
            extract($this->data, EXTR_SKIP);

            // Almacena el contenido generado por la plantilla en un búfer de salida.
            ob_start();
            include $tmpFile;

            // Si la opción de depuración está desactivada, elimina el archivo temporal.
            if (!DEBUG) {
                unlink($tmpFile);
            }

            // Devuelve el contenido generado por la plantilla.
            return ob_get_clean();
        }
    }

    /**
     * Dibujar archivo.
     *
     * @param string $file el archivo
     *
     * @return string el resultado del dibujo
     */
    public function draw(string $file): string
    {
        // Ejecuta el archivo y obtiene el resultado
        $result = $this->__run($file);
        return $result;
    }

    /**
     *  Crea un comentario que no sea visible.
     *
     * @param string $content el contenido
     *
     * @return string
     */
    public function comment(string $content): string
    {
        // Devuelve nulo ya que la función no hace nada con el contenido
        return null;
    }

    /**
     *  Busca etiquetas.
     *
     * @param string $content el contenido
     *
     * @return bool
     */
    private function __searchTags(string $content): bool
    {
        // Verifica si el contenido tiene alguna etiqueta definida
        foreach ($this->tags as $regexp => $replace) {
            if (preg_match('#' . $regexp . '#sU', $content, $matches)) {
                return true;
            }
        }
        // Si no se encontró ninguna etiqueta, devuelve false
        return false;
    }

    /**
     * Reemplazar la notación de puntos en una variable con la notación de corchetes.
     *
     * @param string $var la variable en notación de puntos
     *
     * @return string la variable en notación de corchetes
     */
    private function __replaceVariable(string $var): string
    {
        // Verificar si la variable ya está en notación de corchetes
        if (false === strpos($var, '.')) {
            return $var;
        }

        // Reemplazar los puntos por corchetes
        return preg_replace('/\.([a-zA-Z\-_0-9]*(?![a-zA-Z\-_0-9]*(\'|\")))/', "['$1']", $var);
    }
}