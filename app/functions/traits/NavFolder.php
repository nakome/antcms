<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait NavFolder
{
    /**
     * Metodo navFolder
     *
     * @return void
     */
    public static function navFolder(): void
    {
        $url_base = AntCMS::urlBase();
        $url_current = AntCMS::urlCurrent();
        $url_segment = (AntCMS::urlSegment(0) !== '') ? trim(AntCMS::urlSegment(0)) : '';
        $url_folder = $url_base . '/' . $url_segment;
        $source = CONTENT . '/' . $url_segment;
        // comprobar si existe la carpeta
        if (is_dir($source)) {
            // Obtenemos array de segment
            $pages = AntCMS::run()->pages($url_segment, 'date', 'DESC', ['index', '404']);
            // Comprobamos si es array y si es mayor de 1
            if (is_array($pages) && count($pages) > 2) {
                // iniciamos el html
                $html = '<ul>';
                // Loopeamos y añadimos enlaces
                foreach ($pages as $k => $v) {
                    // Creamos slug
                    $slug = trim($url_segment . '/' . $pages[$k]['slug']);
                    // Quitamos si existe //
                    $slug = str_replace('//', '/', $slug);
                    // Comprobamos que es igual la url
                    if ($url_current == $slug) {
                        // flechas
                        $leftArrow = self::run()->leftArrow();
                        $rightArrow = self::run()->rightArrow();
                        // Si no retorna null vemos el boton normal
                        // en cambio si retorna null le añadimos disabled
                        if (isset($pages[$k - 1]) != null) {
                            $html .= '<li><a class="contrast" href="' . $url_folder . '/' . $pages[$k - 1]['slug'] . '" title="' . $pages[$k - 1]['title'] . '">' . $leftArrow . '</a></li>';
                        } else {
                            $html .= '<li>' . $leftArrow . '</li>';
                        }
                        // Boton drch
                        if (isset($pages[$k + 1]) != null) {
                            $html .= '<li><a class="contrast" href="' . $url_folder . '/' . $pages[$k + 1]['slug'] . '" title="' . $pages[$k + 1]['title'] . '">' . $rightArrow . '</a></li>';
                        } else {
                            $html .= '<li>' . $rightArrow . '</li>';
                        }
                    }
                }
                $html .= '</ul>';
                echo $html;
            }
        }
    }

}
