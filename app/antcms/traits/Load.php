<?php

declare (strict_types = 1);

namespace Traits;

use InvalidArgumentException;

defined('SECURE') or die('No tiene acceso al script.');

trait Load
{

    /**
     * Carga plantilla
     *
     * @param string $route
     *
     * @return void
     */
    protected function _loadTemplating(
        string $route
    ): void {
        if (file_exists($route) && is_file($route)) {
            static::$templating = (require_once $route);
        } else {
            throw new InvalidArgumentException('Oops.. Donde esta el archivo de la plantilla de templating ?!', 100);
        }
    }

    /**
     * Cargar configuración.
     *
     * @param string $route
     *
     * @return void
     */
    protected function _loadConfig(
        string $route
    ): void {
        if (file_exists($route) && is_file($route)) {
            static::$config = (require_once $route);
        } else {
            throw new InvalidArgumentException('Oops.. Donde esta el archivo de configuración ?!');
        }
    }

    /**
     * Cargamos los funciones de la plantilla.
     *
     * @return void
     */
    protected function _loadThemeFunctions(): void
    {
        // carga las funciones de la plantilla
        $template_functions = ROOT . '/app/functions/Functions.php';
        if (file_exists($template_functions) && is_file($template_functions)) {
            require_once $template_functions;
        } else {
            throw new InvalidArgumentException('Oops.. Falta el archivo functions en la plantilla');
        }
    }
}
