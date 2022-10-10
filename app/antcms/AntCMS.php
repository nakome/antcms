<?php

declare (strict_types = 1);

namespace AntCms;

use AntTpl\AntTPL as AntTPL;

defined('SECURE') or die('No tiene acceso al script.');

require_once __DIR__ . '/IAntCMS.php';

require_once __DIR__ . '/traits/Action.php';
require_once __DIR__ . '/traits/Cors.php';
require_once __DIR__ . '/traits/Url.php';
require_once __DIR__ . '/traits/Arr.php';
require_once __DIR__ . '/traits/ScanFile.php';
require_once __DIR__ . '/traits/File.php';
require_once __DIR__ . '/traits/Text.php';
require_once __DIR__ . '/traits/Pages.php';
require_once __DIR__ . '/traits/Filter.php';
require_once __DIR__ . '/traits/Run.php';
require_once __DIR__ . '/traits/Parse.php';
require_once __DIR__ . '/traits/Load.php';
require_once __DIR__ . '/traits/Error.php';
require_once __DIR__ . '/traits/Eval.php';

/**
 * AntCMS.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
class AntCMS implements IAntCMS
{
    use \Traits\Action;
    use \Traits\Cors;
    use \Traits\Url;
    use \Traits\Arr;
    use \Traits\File;
    use \Traits\Text;
    use \Traits\Filter;
    use \Traits\Pages;
    use \Traits\Error;
    use \Traits\Run;
    use \Traits\ScanFile;
    use \Traits\Parse;
    use \Traits\Load;
    use \Traits\EvalPhp;
     
    // separador de datos y contenido
    private const SEPARATOR = '----';

    public static $config;
    // public templating
    public static $templating;

    protected static $_filters = [];
    private static $__actions = [];

    // Variables cabecera
    private $__headers = [
        'title' => 'Title',
        'description' => 'Description',
        'tags' => 'Tags',
        'author' => 'Author',
        'image' => 'Image', // href file
        'date' => 'Date',
        'robots' => 'Robots',
        'keywords' => 'Keywords',
        'category' => 'Category',
        'template' => 'Template', // index,post
        'published' => 'Published', // true, false
        'background' => 'Background', // blue, #f55,rgb(0,0,0)
        'video' => 'Video', // src file
        'color' => 'Color', // blue, #f55,rgb(0,0,0)
        'css' => 'Css', // src file
        'javascript' => 'Javascript', // src file
        'attrs' => 'Attrs', // = [1,2,true,'string']
        'json' => 'Json', // = json file
    ];

    /**
     * Iniciar AntCMS.
     *
     * @param string $path
     *
     * @return void
     */
    public function init(
        string $configFile,
        string $templateConfigFile
    ): void {
        // Cargamos la configuracion
        $this->_loadConfig($configFile);
        $this->_loadTemplating($templateConfigFile);
        $this->_loadThemeFunctions();

        // Zona horaria
        @ini_set('date.timezone', static::$config['timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$config['timezone']);
        } else {
            putenv('TZ=' . static::$config['timezone']);
        }

        // Sanitizamos
        self::runSanitize();

        // charset
        header('Content-Type: text/html; charset=' . static::$config['charset']);
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$config['charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$config['charset']);

        // Session start
        !session_id() and @session_start();

        // Inicializmos AntTPL
        $AntTpl = new AntTPL();
        // Agregamos las etiquetas de config
        $AntTpl->tags = static::$templating;

        // Cargamos la pagina actual
        $page = $this->page(AntCMS::urlCurrent());

        // meta tag generator
        self::actionAdd('meta', fn() => print('<meta name="generator" content="Creado con AntCMS" />'));

        $page['title']??=static::$config['title'];
        $page['tags']??=static::$config['keywords'];
        $page['description']??=static::$config['description'];
        $page['author']??=static::$config['author'];
        $page['image']??='';
        $page['date']??='';
        $page['robots']??='index,follow';
        $page['published']??=false;
        $page['keywords']??=static::$config['keywords'];
        $page['category']??='';
        $page['background']??='';
        $page['video']??='';
        $page['color']??='';
        $page['css']??='';
        $page['javascript']??='';
        $page['attrs']??='{"title":"Hello World"}';
        $page['json']??='';
        // decodificar json
        $page['attrs'] = json_decode($page['attrs'], true);

        $config = self::$config;
        $page['template']??='index';

        // Segmento
        $AntTpl->set('Segment', AntCMS::urlSegment(0));
        // Publicado
        $page['published'] = ($page['published'] == 'true') ? true : false;
        // Comprobamos que este publicado
        if ($page['published']) {
            $AntTpl->set('page', $page);
            $AntTpl->set('config', $config);
            // Comprueba si existe una plantilla definida y si no usa index.html
            $themeFile = THEME . '/' . $page['template'] . '.html';
            if (file_exists($themeFile) and is_file($themeFile)) {
                die($AntTpl->draw($themeFile));
                exit();
            } else {
                die($AntTpl->draw(THEME . '/index.html'));
                exit();
            }
        } else {
            $AntTpl->set('page', $page);
            $AntTpl->set('config', $config);
            $this->errorPage($AntTpl);
        }
    }
}
