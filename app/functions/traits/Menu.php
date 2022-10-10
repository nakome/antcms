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
    private function __createMenu(
        array $nav
    ): string
    {
        $html = '';
        foreach ($nav as $k => $v) {
            // key exists
            if (array_key_exists($k, $nav)) {
                // not empty
                if ('' != $k) {
                    // external page
                    if (preg_match('/http/i', $k)) {
                        $capitalize = (string) ucfirst($v);
                        $html .= <<<HTML
                            <li><a href="{$k}">{$capitalize}</a></li>
                        HTML;
                    } else {
                        // is array
                        if (is_array($v)) {
                            // dropdown
                            $html .= '<li>';
                            $html .= '<a role="button" aria-haspopup="true" aria-expanded="false" href="#">'.ucfirst($k).'</a>';
                            $html .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                            $html .= $this->__createMenu($v);
                            $html .= '</ul>';
                            $html .= '</li>';
                        } else {
                            // active page
                            $active = AntCMS::urlCurrent();
                            $activeurl = str_replace('/', '', $k);
                            if ($active == $activeurl) {
                                $capitalize = (string) ucfirst($v);
                                $urlBase = trim(AntCMS::urlBase().$k);
                                $html .= <<<HTML
                                    <li><a class="active" href="{$urlBase}">{$capitalize}</a></li>
                                HTML;
                            } else {
                                $capitalize = (string) ucfirst($v);
                                $urlBase = trim(AntCMS::urlBase().$k);
                                $html .= <<<HTML
                                    <li><a href="{$urlBase}">{$capitalize}</a></li>
                                HTML;
                            }
                        }
                    }
                }
            }
        }
        // show html
        return (string) $html;
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
