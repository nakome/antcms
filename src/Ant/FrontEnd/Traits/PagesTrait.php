<?php

declare (strict_types = 1);

namespace Ant\FrontEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\File\File as File;
use Vendor\Url\Url as Url;
use Vendor\Sanitize\Sanitize as Sanitize;

trait PagesTrait
{
    /**
     *  Obtener array de páginas.
     *
     * <code>
     *  Ant\FrontEnd\FrontEnd::pages('blog','date','DESC',array('index','404'),null);
     * </code>
     *
     * @param string $url
     * @param string $order_by
     * @param string $order_type
     * @param array  $ignore
     * @param int    $limit
     *
     * @return array
     */
    public function pages(
        string $url,
        string $order_by = 'date',
        string $order_type = 'ASC',
        array $ignore = ['404'],
        int $limit = 0
    ): array
    {
        // obtener un array de encabezados
        $headers = $this->__headers;

        // escanear carpeta content
        $pages = File::scan(CONTENT . '/' . $url, 'html');

        $_pages = array_filter(array_map(function ($key, $page) use ($ignore, $headers) {
            // ignorar los que no queramos
            if (in_array(basename($page, '.html'), $ignore)) {
                return null;
            }

            // obtenemos el contenido
            $content = Sanitize::file($page);
            // lo dividimos en una array
            $_headers = explode(self::SEPARATOR, $content);

            $pageData = [];
            // bucle del array
            foreach ($headers as $campo => $regex) {
                if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                    $pageData[$campo] = trim($match[1]);
                } else {
                    $pageData[$campo] = '';
                }
            }

            // Si el usuario no escribe Fecha, usamos filetime
            if (!$pageData['date']) {
                $pageData['date'] = filemtime($page);
            } else {
                $date = str_replace('/', '-', $pageData['date']);
                $pageData['date'] = strtotime($date);
            }

            // convertir local a url
            $site_url = rtrim(Url::base(), '/');
            // Obtener la URL de la página
            $url = Url::parse($site_url, $page);

            $pageData['content'] = $this->_parseContent($content);
            $pageData['url'] = $url;
            $pageData['slug'] = basename($page, '.html');

            return $pageData;
        }, array_keys($pages), $pages));


        // ordenar por campo
        usort($_pages, function ($a, $b) use ($order_by, $order_type) {
            if($order_by === 'date'){
                if ($order_type === 'ASC') {
                    return $a[$order_by] - $b[$order_by];
                } else {
                    return $b[$order_by] - $a[$order_by];
                }
            }else{
                if ($order_type === 'ASC') {
                    return strcmp($a[$order_by], $b[$order_by]);
                } else {
                    return strcmp($b[$order_by], $a[$order_by]);
                }
            }
        });

        // limitar el número de resultados
        if ($limit > 0) {
            $_pages = array_slice($_pages, 0, $limit);
        }

        return $_pages;
    }

    /**
     * Obtiene la página solicitada.
     *
     * <code>
     *   Ant\FrontEnd\FrontEnd::page('blog');
     * </code>
     *
     * @param string $url
     * @return array
     */
    public function page(string $url): array
    {
        $headers = $this->__headers;
        $content = '';
        $slug = '';
        $date = '';

        // Determinar la ubicación del archivo
        if ($url) {
            $file = CONTENT . '/' . $url;
            if (is_dir($file)) {
                $file .= '/index.html';
            } else {
                $file .= '.html';
            }
        } else {
            $file = CONTENT . '/index.html';
        }

        // Cargar el contenido del archivo
        if (file_exists($file)) {
            $content = Sanitize::file($file);
        } else {
            $file = CONTENT . '/404.html';
            if (file_exists($file)) {
                $content = Sanitize::file($file);
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } elseif (file_exists(CONTENT . '/' . Url::segment(0) . '/404.html')) {
                $content = Sanitize::file(CONTENT . '/' . Url::segment(0) . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } else {
                $content = Sanitize::file(CONTENT . '/' . HTML_LANG . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }

        // Parsear los headers de la página
        $_headers = explode(self::SEPARATOR, $content);
        foreach ($headers as $campo => $regex) {
            if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                $page[$campo] = trim($match[1]);
            } else {
                $page[$campo] = '';
            }
        }

        // Obtener la URL de la página
        $site_url = rtrim(Url::base(), '/');

        // Obtener la URL de la página
        $url = Url::parse($site_url, $file);

        // Obtener la URL de la página
        $pages['url'] = $url;

        // Obtener la fecha de la página
        if (!$page['date']) {
            $date = filemtime((string) $page['date']);
        } else {
            $date = strtotime(str_replace('/', '-', $page['date']));
        }
        $page['date'] = date('d-m-Y', (int) $date);

        // Obtener el contenido de la página
        $_content = $this->_parseContent($content);
        if (is_array($_content)) {
            $page['content_short'] = $_content['content_short'];
            $page['content'] = $_content['content_full'];
        } else {
            $page['content_short'] = $_content;
            $page['content'] = $_content;
        }

        // Obtener el slug de la página
        $page['slug'] = basename($file, '.html');

        return $page;
    }

}