<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;


defined('SECURE') or die('No tiene acceso al script.');


Trait Articles
{

    /**
     * Comprobar vacio
     *
     * @return ?string
     */
    private function __emptyPage():string
    {
        return array_key_exists('page', $_GET) ? $_GET['page'] : '0';
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
        ?float $pag,
        ?string $txt
    ): string {
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
    private function __tplPag(
        array $posts,
        int $limit,
        int $num
    ): void {
        // Total = post / limit - 1
        $total = ceil(count($posts) / $limit);

        // flechas
        $leftArrow = $this->leftArrow();
        $rightArrow = $this->rightArrow();

        // Si esta vacia la primera pagina
        $p = $this->__emptyPage();

        if (count($posts) > $num) {
            // Inicializamos paginacion
            $pagination = '<nav aria-label="Paginacion"><ul class="pagination">';
            $disabled = (0 == $p) ? "disabled" : "";
            $pag = $p - 1;
            $pagination .= $this->__listItem("page-item {$disabled}", "page-link", $pag, $leftArrow);

            if ($p > 0) {
                $pagination .= $this->__listItem("page-item {$disabled}", "page-link", 0, "Primera");
            }

            // Lopeamos numeros
            $s = max(1, $p - 5);
            for (; $s < min($p + 6, ($total - 1)); $s++) {
                if ($s == $p) {
                    $class = ($p == $s) ? "page-item active" : "page-item";
                    $pagination .= $this->__listItem("page-item active", $class, $s, $s);

                } else {
                    $class = ($p == $s) ? "active" : "";
                    $pagination .= $this->__listItem($class, "page-link", $s, $s);
                }
            }

            // Ultima
            if ($p < ($total - 1)) {
                $t = $total - 1;
                $pagination .= $this->__listItem("page-item", "page-link", $t, "Ultima");
            }

            // Flecha derecha
            $disabled = (($total - 1) == $p) ? "disabled" : "";
            $pag = $p + 1;
            $pagination .= $this->__listItem("page-item {$disabled}", "page-link", $pag, $rightArrow);
            $pagination .= '</ul></nav>';
            echo $pagination;
        }
    }

    /**
     * Renderizar paginación
     *
     * @param array $items
     *
     * @return void
     */
    private function __tplPosts(
        array $items
    ): void {
        // inicializamos la plantilla
        $html = '';
        foreach ($items as $articulo) {

            if (
                $articulo['published'] !== (string) 'true' &&
                $articulo['published'] !== (bool) true &&
                $articulo['published'] !== (int) 1
            ) {
                continue;
            }

            // variables
            $slug = $articulo['url'];
            $title = $articulo['title'];
            $description = AntCMS::short($articulo['description'], 80);
            $image = $articulo['image'];
            $date = date('d-m-Y', $articulo['date']);
            $author = $articulo['author'];
            $tags = $articulo['tags'];
            $keywords = $articulo['keywords'];
            $published = $articulo['published'];
            $background = $articulo['background'];
            $color = $articulo['color'];
            $site_url = AntCMS::urlBase();
            // separamos tags en array
            $arrayOfTags = explode(',', $articulo['tags']);
            // plantilla tags
            $htmlTemplateTags = '';
            if (is_array($arrayOfTags)) {
                foreach ($arrayOfTags as $tag) {
                    $htmlTemplateTags .= <<<HTML
                        <a
                            class="badge bg-primary text-decoration-none me-1"
                            href="{$site_url}/blog?buscar={$tag}">
                            {$tag}
                        </a>
                    HTML;
                }
            }
            // plantilla titulo
            $htmlTemplateTitle = <<<HTML
                <h2>
                    <a
                        class="contrast"
                        href="{$slug}">
                        {$title}
                    </a>
                </h3>
            HTML;
            // plantilla fecha
            $htmlTemplateDate = <<<HTML
                <a
                    href="{$site_url}/blog?buscar={$date}">
                    <time datetime="{$date}">{$date}</time>
                </a>
            HTML;
            // plantilla articulo
            $html .= <<<HTML
                <article class="post">
                    {$htmlTemplateTitle}
                    <p>{$description}</p>
                    <footer class="post-footer">
                        <small>
                            <strong>Date:&nbsp;</strong>
                            {$htmlTemplateDate}&nbsp;
                            <strong> Tags:&nbsp;</strong>
                            {$htmlTemplateTags}
                        </small>
                    </footer>
                </article>
            HTML;
        }
        $html .= '';
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
    public static function articles(
        string $name,
        int $num
    ): void {
        // Obtenemos el array de la carpeta
        $posts = AntCMS::run()->pages($name, 'date', 'DESC', ['index', '404']);
        // Limite de paginas
        $limit = $num;
        // Inicializamos blogPosts
        $blogPosts = [];
        if (is_array($posts) && count($posts) > 0) {
            // Push en blogPosts
            foreach ($posts as $f) {
                array_push($blogPosts, $f);
            }
            // Divide en fragmentos
            $files = array_chunk($blogPosts, $limit);
            // Obtenemos pagina
            $pgkey = array_key_exists('page', $_GET) ? $_GET['page'] : 0;
            $items = $files[$pgkey];
            // plantilla carpeta
            self::run()->__tplPosts($items);
            // Paginacion carpeta
            self::run()->__tplPag($posts, $limit, $num);
        }
    }

}
