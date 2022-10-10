<?php

declare (strict_types = 1);

namespace App\Functions;

use AntCms\AntCMS as AntCMS;
use Functions\IFunctions as IFunctions;

defined('SECURE') or die('No tiene acceso al script.');

require_once __DIR__ . '/IFunctions.php';

// traits
require_once __DIR__ . '/traits/Articles.php';
require_once __DIR__ . '/traits/NavFolder.php';
require_once __DIR__ . '/traits/Menu.php';
require_once __DIR__ . '/traits/Search.php';
require_once __DIR__ . '/traits/Contact.php';

/**
 * Funciones de el tema
 */
class Functions implements IFunctions
{

    use \Traits\Articles;
    use \Traits\NavFolder;
    use \Traits\Menu;
    use \Traits\Search;
    use \Traits\Contact;

    /**
     * Retorna static
     *
     * @return static
     */
    public static function run(): self
    {
        return new static;
    }

    /**
     * Flecha izq.
     *
     * @return string
     * @abstract
     */
    public function leftArrow(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/></svg>';
    }
    /**
     * Flecha drch.
     *
     * @return string
     * @abstract
     */
    public function rightArrow(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/></svg>';
    }

    /**
     * Cargando las funciones
     *
     * @return void
     */
    public static function init(): void
    {
        AntCMS::actionAdd(
            'articles',
            "App\Functions\Functions::articles"
        );
        AntCMS::actionAdd(
            'navFolder',
            "App\Functions\Functions::navFolder"
        );
        AntCMS::actionAdd(
            'navigation',
            "App\Functions\Functions::menu"
        );
        AntCMS::actionAdd(
            'contact',
            "App\Functions\Functions::contact"
        );
        AntCMS::actionAdd(
            'theme_before',
            "App\Functions\Functions::query"
        );
    }
}

Functions::init();
