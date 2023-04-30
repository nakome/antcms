<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait NavFolder
{
/**
 * Genera un enlace de navegación entre las páginas de una misma carpeta en el CMS.
 * Si la carpeta no existe o tiene menos de dos páginas, no muestra nada.
 * La navegación se muestra en forma de flechas hacia la página anterior y posterior a la actual.
 *
 * @return void
 */
    public static function navFolder(): void
    {
        // Obtiene el segmento de la URL correspondiente a la carpeta actual
        $url_segment = AntCMS::urlSegment(0);

        // Si el segmento no existe o la carpeta no existe, no muestra nada
        if (!$url_segment || !is_dir(CONTENT . '/' . $url_segment)) {
            return;
        }

        // Obtiene las páginas dentro de la carpeta actual, ordenadas por fecha descendente y excluyendo las páginas index y 404
        $pages = AntCMS::run()->pages($url_segment, 'date', 'DESC', ['index', '404']);

        // Si la carpeta tiene menos de dos páginas, no muestra nada
        if (count($pages) <= 2) {
            return;
        }

        // Obtiene la URL base de la carpeta actual y la URL de la página actual
        $url_folder = AntCMS::urlBase() . '/' . $url_segment;
        $url_current = AntCMS::urlCurrent();

        // Crea el inicio del HTML para la navegación
        $html = '<ul>';

        // Recorre cada página de la carpeta
        foreach ($pages as $k => $page) {

            // Obtiene el slug de la página actual
            $slug = trim($url_segment . '/' . $page['slug'], '/');

            // Si la página actual no es la página actualmente visitada, continua con la siguiente
            if ($url_current !== $slug) {
                continue;
            }

            // Obtiene las flechas de navegación y las páginas anterior y posterior a la actual
            $leftArrow = self::run()->leftArrow();
            $rightArrow = self::run()->rightArrow();
            $prevPage = $pages[$k - 1] ?? null;
            $nextPage = $pages[$k + 1] ?? null;

            // Crea los enlaces a la página anterior y posterior si existen, sino, solo muestra las flechas
            // Comprueba que este publicada tambien
            $prevLink = ($prevPage && $prevPage['published']) ? '<a class="contrast" href="' . $url_folder . '/' . $prevPage['slug'] . '" title="' . $prevPage['title'] . '">' . $leftArrow . '</a>' : $leftArrow;
            $nextLink = ($nextPage && $nextPage['published']) ? '<a class="contrast" href="' . $url_folder . '/' . $nextPage['slug'] . '" title="' . $nextPage['title'] . '">' . $rightArrow . '</a>' : $rightArrow;

            // Agrega los enlaces a la navegación en formato HTML
            $html .= '<li>' . $prevLink . '</li><li>' . $nextLink . '</li>';
        }

        // Cierra el HTML para la navegación y lo muestra en pantalla
        $html .= '</ul>';
        echo $html;
    }

}
