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
    private function __renderQuery(array $results): string
    {
        // Se inicializa la variable $html con una lista HTML
        $html = '<ul>';
        
        // Se recorren los resultados y se construye un HTML por cada página
        foreach ($results as $page) {
            // Se construye la URL de la página
            $url = AntCMS::urlBase() . rtrim(str_replace('//', '/', str_replace(AntCMS::urlBase(), '', $page['url'])), '/');
            // Se convierte la fecha de la página en formato dd-mm-aaaa
            $dateFormat = date('d-m-Y', (int)$page['date']);
            // Se agrega el HTML de la página a la lista
            $html .= <<<HTML
                <li>
                    <strong>{$dateFormat}</strong>
                    <span> - </span>
                    <a href="{$url}">{$page['title']}</a>
                </li>
            HTML;
        }
        
        // Se cierra la lista HTML y se devuelve el resultado
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
        // Verifica si se ha enviado una consulta de búsqueda
        if (!isset($_GET['buscar'])) {
            return;
        }
    
        // Limpia y almacena la consulta de búsqueda
        $query = trim($_GET['buscar']);
        if (empty($query)) {
            return;
        }
    
        // Obtiene todas las páginas del sitio, ordenadas por fecha descendente, y filtra las que tienen el estado "404"
        $pages = AntCMS::run()->pages('/', 'date', 'DESC', ['404'], 0);
    
        // Filtra las páginas que contienen la consulta de búsqueda en cualquiera de sus campos relevantes
        $results = array_filter($pages, function ($page) use ($query) {
            return preg_match("/$query/i", $page['title']) ||
            preg_match("/$query/i", $page['description']) ||
            preg_match("/$query/i", $page['tags']) ||
            preg_match("/$query/i", $page['author']) ||
            preg_match("/$query/i", $page['keywords']) ||
            preg_match("/$query/i", $page['slug']) ||
            preg_match("/$query/i", date('d-m-Y', (int)$page['date']));
        });
    
        // Obtiene el número total de resultados y genera el HTML de salida
        $total = count($results);
        $searchOutput = self::run()->__renderQuery($results);
        $html = "<article>{$searchOutput}<footer><strong>{$total}</strong> resultado/s de {$query}</footer></article>";
    
        // Si no se encontraron resultados, muestra un mensaje apropiado
        if (!$results) {
            $html = "<article><h3>No hay resultados de {$query}</h3></article>";
        }
    
        // Imprime el HTML generado
        echo $html;
    }
}
