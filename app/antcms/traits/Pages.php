<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Pages
{
    /**
     *  Obtener array de páginas.
     *
     * <code>
     *  AntCMS::pages('blog','date','DESC',array('index','404'),null);
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
        string $order_type = 'DESC',
        array $ignore = ['404'],
        int $limit = 0
    ):array 
    {
        // obtener un array de encabezados
        $headers = $this->__headers;
        // escanear carpeta content
        $pages = self::scanFiles(CONTENT . '/' . $url, 'html');
        $_pages = [];
        // bucle
        foreach ($pages as $key => $page) {
            // ignorar los que no queramos
            if (!in_array(basename($page, '.html'), $ignore)) {
                // obtenemos el contenido
                $content = file_get_contents($page);
                // lo dividimos en una array
                $_headers = explode(self::SEPARATOR, $content);
                // bucle del array
                foreach ($headers as $campo => $regex) {
                    if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                        $_pages[$key][$campo] = trim($match[1]);
                    } else {
                        $_pages[$key][$campo] = '';
                    }
                }
                
                // Si la usuario no escribe Fecha use filetime
                if (!$_pages[$key]['date']) {
                    $_pages[$key]['date'] = filemtime((string)$page);
                } else {
                    $date = str_replace('/', '-', $_pages[$key]['date']);
                    $_pages[$key]['date'] = strtotime($date);
                }

                // convertir local a url
                $site_url = rtrim(self::urlBase(), '/');
                $url = str_replace(CONTENT, $site_url, $page);
                $url = str_replace('index.html', '', $url);
                $url = str_replace('.html', '', $url);
                $url = str_replace('\\', '/', $url);
                $url = rtrim($url, '/');

                $_pages[$key]['content'] = $this->_parseContent($content);
                $_pages[$key]['url'] = $url;
                $_pages[$key]['slug'] = basename($page, '.html');
            }
        }
        // verificar si la matriz es más de 1
        $_pages = (count($_pages) > 0) ? self::shortArray($_pages, $order_by, $order_type) : [];
        if (null != $limit) {
            $_pages = array_slice(
                (array)$_pages, 
                (int) 0, 
                (int)$limit
            );
        }
        return $_pages;
    }

    /**
     *  Obtener la pagina.
     *
     * <code>
     *  AntCMS::page('blog');
     * </code>
     *
     * @param string $url
     *
     * @return array
     */
    public function page(
        string $url
    ): array
    {
        $headers = $this->__headers;
        $file = $url ? CONTENT . '/' . $url : CONTENT . '/' . 'index';

        // Load the file
        if (is_dir($file)) {
            $file = CONTENT . '/' . $url . '/index.html';
        } else {
            $file .= '.html';
        }

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $file = CONTENT . '/404.html';
            if (file_exists($file)) {
                $content = file_get_contents($file);
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } elseif (file_exists(CONTENT . '/' . self::urlSegment(0) . '/404.html')) {
                $content = file_get_contents(CONTENT . '/' . self::urlSegment(0) . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } else {
                $content = file_get_contents(CONTENT . '/' . self::$config['lang'] . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }
        $_headers = explode(self::SEPARATOR, $content);
        foreach ($headers as $campo => $regex) {
            if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                $page[$campo] = trim($match[1]);
            } else {
                $page[$campo] = '';
            }
        }

        $url = str_replace(CONTENT, static::urlBase(), $file);
        $url = str_replace('index.html', '', $url);
        $url = str_replace('.html', '', $url);
        $url = str_replace('\\', '/', $url);
        $url = rtrim($url, '/');
        $pages['url'] = $url;

        $date = '';
        if (!$page['date']) {
            $date = filemtime((string)$page['date']);
        } else {
            $date = strtotime(str_replace('/', '-', $page['date']));
        }
        $page['date'] = date('d-m-Y', (int)$date);

        $_content = $this->_parseContent($content);
        if (is_array($_content)) {
            $page['content_short'] = $_content['content_short'];
            $page['content'] = $_content['content_full'];
        } else {
            $page['content_short'] = $_content;
            $page['content'] = $_content;
        }
        $page['slug'] = basename($file, '.html');

        return $page;
    }
}
