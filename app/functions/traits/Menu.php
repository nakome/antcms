<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait Menu
{

    /**
     * Transformar array to menu.
     *
     * @param array $nav
     */
    private function __createMenu(array $nav): string
    {
        $html = '';
        // iterar sobre los elementos del menú
        foreach ($nav as $k => $v) {
            // ignorar elementos vacíos o que no sean válidos
            if (empty($k) || !is_string($k) || empty($v)) {
                continue;
            }
            // si el elemento del menú es un enlace externo
            if (preg_match('/http/i', $k)) {
                // agregar un enlace
                $html .= '<a href="' . $k . '">' . ucfirst($v) . '</a>';
            } else {
                // obtener la URL base del sitio y la URL actual
                $urlBase = AntCMS::urlBase() . $k;
                $currentUrl = AntCMS::urlCurrent();
                // verificar si el elemento actual del menú está activo
                $active = ($currentUrl == str_replace('/', '', $k)) ? 'class="active"' : '';
                // si el elemento del menú es un submenú
                if (is_array($v)) {
                    // agregar el submenú
                    $html .= '<li>';
                    $html .= '<a role="button" aria-haspopup="true" aria-expanded="false" href="#">' . ucfirst($k) . '</a>';
                    $html .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    $html .= $this->__createMenu($v);
                    $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    // agregar un elemento de menú
                    $html .= '<a ' . $active . ' href="' . $urlBase . '">' . ucfirst($v) . '</a>';
                }
            }
        }
        return $html;
    }

    /**
     * Navegación
     *
     * @return void
     */
    public static function menu(): void
    {
        $arr = (array)AntCMS::$config['menu'];
        $html = (string)self::run()->__createMenu($arr);
        echo $html;
    }

}
