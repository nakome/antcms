<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait Articles
{

    /**
     * Comprobar vacio
     *
     * @return ?string
     */
    private function __emptyPage(): string
    {
        return $_GET['page'] ?? '0';
    }

    /**
     * Lista li
     *
     * @param string $liclass
     * @param string $linkclass
     * @param int|float $pag
     * @param string|int $txt
     *
     * @return string
     */
    private function __listItem(
        string $liclass,
        string $linkclass,
        ? float $pag,
        mixed $txt
    ) : string {
        return <<<HTML
            <li class="{$liclass}">
                <a class="{$linkclass}" href="?page={$pag}">
                    {$txt}
                </a>
            </li>
        HTML;
    }

    /**
     * Renderizar paginación
     *
     * @param array $posts
     * @param int $limit
     * @param int $num
     *
     * @return void
     */
    private function __tplPag(array $posts, int $limit, int $num): void
    {
        // Calcular el número total de páginas necesarias
        $totalPages = ceil(count($posts) / $limit);
        // Establecer la página actual a mostrar
        $currentPage = $this->__emptyPage();
        // Establecer la página anterior y siguiente
        $prevPage = $currentPage - 1;
        $nextPage = $currentPage + 1;

        // Si el número de publicaciones es menor o igual que el número de publicaciones especificado, no hay necesidad de mostrar paginación
        if (count($posts) <= $num) {
            return;
        }

        // Iniciar la lista de paginación
        echo '<nav aria-label="Paginacion"><ul class="pagination">';

        // Mostrar la página anterior
        echo $this->__listItem("page-item " . ($prevPage < 0 ? "disabled" : ""), "page-link", $prevPage, $this->leftArrow());

        // Mostrar el botón "Primera" si no estamos en la primera página
        if ($currentPage > 0) {
            echo $this->__listItem("page-item", "page-link", 0, "Primera");
        }

        // Calcular qué páginas mostrar en la lista
        $startPage = max(1, $currentPage - 5);
        $endPage = min($currentPage + 6, $totalPages);

        // Mostrar cada página en la lista
        for ($i = $startPage; $i < $endPage; $i++) {
            // Determinar si esta página es la actual
            $isActive = $i === $currentPage;
            // Establecer la clase de la página
            $class = "page-item" . ($isActive ? " active" : "");
            // Mostrar la página
            echo $this->__listItem($class, "page-link", $i, $i);
        }

        // Mostrar el botón "Última" si no estamos en la última página
        if ($currentPage < $totalPages - 1) {
            echo $this->__listItem("page-item", "page-link", $totalPages - 1, "Ultima");
        }

        // Mostrar la página siguiente
        echo $this->__listItem("page-item " . ($nextPage >= $totalPages ? "disabled" : ""), "page-link", $nextPage, $this->rightArrow());
        // Cerrar la lista de paginación
        echo '</ul></nav>';
    }

    /**
     * Renderizar paginación
     *
     * @param array $items
     *
     * @return void
     */
    private function __tplPosts(array $items): void
    {
        // Inicializar variable vacía para concatenar los artículos
        $html = '';

        // Recorrer cada artículo y construir su HTML
        foreach ($items as $articulo) {

            // Si el artículo no está publicado, pasar al siguiente
            if (!in_array($articulo['published'], ['true', true, 1])) {
                continue;
            }

            // Obtener la URL base del sitio
            $site_url = AntCMS::urlBase();

            // Convertir la lista de tags en HTML
            $tags = explode(',', $articulo['tags']);
            $tags_html = '';
            foreach ($tags as $tag) {
                $tags_html .= "<a class=\"badge bg-primary text-decoration-none me-1\" href=\"{$site_url}/blog?buscar={$tag}\">{$tag}</a>";
            }

            // Formatear la fecha
            $date = date('d-m-Y', $articulo['date']);

            // Truncar el texto del artículo a 80 caracteres
            $txtShort = AntCMS::short($articulo['description'], 80);

            // Concatenar el HTML del artículo a la variable $html
            $html .= <<<HTML
            <article class="post">
                <h2><a class="contrast" href="{$articulo['url']}">{$articulo['title']}</a></h2>
                <p>{$txtShort}</p>
                <footer class="post-footer">
                    <small>
                        <strong>Date:&nbsp;</strong><a href="{$site_url}/blog?buscar={$date}"><time datetime="{$date}">{$date}</time></a>&nbsp;
                        <strong>Tags:&nbsp;</strong>{$tags_html}
                    </small>
                </footer>
            </article>
            HTML;
        }

        // Imprimir el HTML generado
        echo $html;
    }

    /**
     * Articles
     *
     * @param string $name
     * @param int $num
     *
     * @return void
     */
    public static function articles(string $name, int $num): void
    {
        // Obtener las páginas con el nombre especificado, ordenadas por fecha de forma descendente
        // y excluyendo las páginas con nombre "index" y "404"
        $posts = AntCMS::run()->pages($name, 'date', 'DESC', ['index', '404']);

        // Establecer el límite de artículos por página
        $limit = $num;

        // Obtener el número de página actual a través del parámetro "page" en la URL
        $pgkey = $_GET['page'] ?? 0;

        // Si se encontraron páginas
        if (is_array($posts) && count($posts) > 0) {
            // Dividir las páginas en bloques de acuerdo al límite de artículos por página
            $files = array_chunk($posts, $limit);

            // Mostrar los artículos del bloque correspondiente a la página actual
            self::run()->__tplPosts($files[$pgkey]);

            // Mostrar la paginación
            self::run()->__tplPag($posts, $limit, $num);
        }
    }

}
