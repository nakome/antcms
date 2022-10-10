<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait Search
{
    /**
     * Resultados busqueda
     *
     * @return string
     */
    private function __renderQuery(
        array $results
    ): string
    {
        $html = '<ul>';
        foreach ($results as $page) {
            // quitamos doble slash si hay
            $url = str_replace(AntCMS::urlBase(), '', $page['url']);
            $url = str_replace('//', '/', $url);
            $url = AntCMS::urlBase() . $url;
            $url = rtrim($url, '/');
            // creamos un boton para ir a la pagina con $page['url']
            $dateFormat = date('d-m-Y', (int)$page['date']);
            $html .= <<<HTML
                <li>
                    <strong>{$dateFormat}</strong>
                    <span> - </span>
                    <a href="{$url}">{$page['title']}</a>
                </li>
            HTML;
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Buscar
     *
     * @return void
     */
    public static function query(): void
    {
        // en la barra de busqueda seria algo asi
        // http://localhost/AntCMSAntCMS/?buscar=
        if (array_key_exists('buscar', $_GET)) {
            // el nombre a buscar
            // http://localhost/AntCMSAntCMS/?buscar=Hola
            // $query = hola
            $query = $_GET['buscar'];
            // comprobamos que hay algo para buscar
            if ($query) {
                // obtenemos todas las paginas que hay en la carpeta content
                // si quisiéramos buscar solo en artículos usamos /articulos
                $data = AntCMS::run()->pages('/', 'date', 'DESC', ['404'], 0);
                // cogemos las 5 primeras letras
                $name = urlencode(trim($query));
                // iniciamos el array y el total
                $results = [];
                $total = 0;
                // hacemos un loop y buscamos en los resultados
                foreach ($data as $item) {
                    // remplazamos la direccion local por la url del dominio
                    $root = str_replace(AntCMS::urlBase(), CONTENT, $item['url']);
                    // decodificamos la url
                    $name = urldecode($name);
                    // comprobamos que exista con preg_match
                    // fecha
                    if (preg_match("/$name/i", date('d-m-Y', (int)$item['date'])) ||
                        preg_match("/$name/i", $item['title']) ||
                        preg_match("/$name/i", $item['description']) ||
                        preg_match("/$name/i", $item['tags']) ||
                        preg_match("/$name/i", $item['author']) ||
                        preg_match("/$name/i", $item['keywords']) ||
                        preg_match("/$name/i", $item['slug'])) {
                        // si hay éxito lo ponemos en el array
                        $results[] = [
                            'title' => (string)$item['title'],
                            'description' => (string)$item['description'],
                            'date' => (string)$item['date'],
                            'url' => (string)$item['url'],
                        ];
                        // contamos los resultados
                        ++$total;
                    }
                }
                // iniciamos el resultado
                $searchOutput = self::run()->__renderQuery($results);
                // plantilla resultados
                $html = <<<HTML
                    <article>
                        {$searchOutput}
                        <footer>
                            <strong>{$total}</strong> resultado/s de {$query}
                        </footer>
                    </article>
                HTML;

                // si hay resultados los enseñamos
                if ($results) {
                    echo $html;
                    // si no ponemos que no hay resultados
                } else {
                    $html .= "<h3>No hay resultados de {$query}</h3>";
                    echo $html;
                }
            }
        }
    }

}
