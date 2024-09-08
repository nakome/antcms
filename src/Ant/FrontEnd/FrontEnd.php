<?php

declare (strict_types = 1);

namespace Ant\FrontEnd;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\Action\Action as Action;
use Vendor\Filter\Filter as Filter;
use Vendor\Template\Template as Template;
use Vendor\Url\Url as Url;

/**
 * FrontEnd.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
final class FrontEnd
{
    // Traits
    use Traits\ErrorPageTrait;
    use Traits\PagesTrait;

    // separador de datos y contenido
    private const SEPARATOR = '----';

    // public templating
    public static $templating;

    // Variables cabecera
    private $__headers = [
        'title'       => 'Title',
        'description' => 'Description',
        'tags'        => 'Tags',
        'author'      => 'Author',
        'image'       => 'Image', // href file
        'date'      => 'Date',
        'robots'      => 'Robots',
        'keywords'    => 'Keywords',
        'category'    => 'Category',
        'template'    => 'Template', // index,post
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
     * Retorna una nueva instancia de la clase actual.
     *
     * @return new static
     */
    public static function run(): object
    {
        return new static();
    }

    /**
     * Parsear contenido.
     *
     * @param string $content el contenido
     *
     * @return $content (array)
     */
    protected function _parseContent(string $content): string
    {
        $_content = '';
        $i        = 0;

        // Dividir el contenido en un array basado en la constante SEPARATOR
        // y volver a concatenar todo el contenido excepto el primer elemento
        foreach (explode(self::SEPARATOR, $content) as $c) {
            0 != $i++ and $_content .= $c;
        }

        $content = $_content;

        // Reemplazar las variables del contenido con los valores correspondientes
        $content = str_replace('{Url}', Url::base(), $_content);
        $content = str_replace('{Email}', CONTACT_EMAIL, $content);

        // Encontrar la posición de la etiqueta de resumen {More} y separar el contenido
        // en dos partes: contenido corto y contenido completo
        $pos = strpos($content, '{More}');

        if (false === $pos) {
            // Aplicar un filtro a todo el contenido
            $content = Filter::apply('content', $content);
        } else {
            $content = explode('{More}', $content);
            // Aplicar un filtro al contenido corto
            $content['content_short'] = Filter::apply('content', $content[0]);
            // Aplicar un filtro al contenido completo
            $content['content_full'] = Filter::apply('content', $content[0] . $content[1]);
        }

        // Eliminar espacios en blanco extra
        //$content = preg_replace('/\s+/', ' ', $content);
        // Evaluar cualquier código PHP en el contenido
        $content = static::_evalPHP($content);

        return $content;
    }

    /**
     * Evalúa las etiquetas {php} y las sustituye por el resultado de la evaluación.
     *
     * @param string $str el string con las etiquetas {php}
     *
     * @return string el string con las etiquetas {php} sustituidas por el resultado de la evaluación
     */
    protected static function _evalPHP(string $str): string
    {
        // Utiliza una expresión regular para buscar todas las etiquetas {php} en $str, y luego utiliza
        // la función '_obEval' para evaluar el código PHP entre esas etiquetas y devolver su resultado.
        return preg_replace_callback('/\\{php\\}(.*?)\\{\\/php\\}/ms', 'Ant\FrontEnd\FrontEnd::_obEval', $str);
    }

    /**
     * Eval Content.
     *
     * @param string $data la cadena de texto que contiene el código PHP a ejecutar
     *
     * @return string el resultado de la evaluación del código PHP
     */
    protected static function _obEval(string $data): string
    {
        // Comenzar el almacenamiento en el búfer de salida
        ob_start();

        // Ejecutar el contenido de la cadena `$data` como código PHP
        eval($data);

        // Obtener el contenido almacenado en el búfer de salida y almacenarlo en la variable `$data`
        $data = ob_get_contents();

        // Detener el almacenamiento en el búfer de salida y limpiar el búfer
        ob_end_clean();

        // Retornar la variable `$data` que contiene el resultado de la evaluación
        return $data;
    }

    /**
     * Corremos la sanitización.
     *
     * @return void
     */
    public static function runSanitize(): void
    {
        $_GET = array_map(['Vendor\Url\Url', 'sanitize'], $_GET);
    }

    /**
     * Iniciar AntCMS.
     *
     * @param string $path
     *
     * @return void
     */
    public function init(array $templatingConfiguration = []): void
    {

        // Cargamos la configuracion
        static::$templating = $templatingConfiguration;

        // Comprobamos si existe el archivo functions
        $functions = ROOT . '/config/functions.php';
        if (file_exists($functions) && is_file($functions)) {
            require $functions;
        }

        // Sanitizamos $_GET
        self::runSanitize();

        // Session start
        !session_id() and @session_start();

        // Inicializmos la clase Template
        $AntTpl = new Template();

        // Agregamos las etiquetas de el archivo template.php
        $AntTpl->tags = static::$templating;

        // Cargamos la pagina actual
        $page = $this->page(Url::current());

        // meta tag generator
        Action::add('meta', fn() => print('<meta name="generator" content="Creado con AntCMS" />'));

        $pageHeadersArray = [
            'title'       => $page['title'] ?? SITE_TITLE,
            'tags'        => $page['tags'] ?? SITE_KEYWORDS,
            'description' => $page['description'] ?? SITE_DESCRIPTION,
            'author'      => $page['author'] ?? AUTHOR,
            'image'       => $page['image'] ?? '',
            'date'        => $page['date'] ?? '',
            'robots'      => $page['robots'] ?? 'index,follow',
            'published'   => $page['published'] ?? false,
            'keywords'    => $page['keywords'] ?? SITE_KEYWORDS,
            'category'    => $page['category'] ?? '',
            'background'  => $page['background'] ?? '',
            'video'       => $page['video'] ?? '',
            'color'       => $page['color'] ?? '',
            'css'         => $page['css'] ?? '',
            'javascript'  => $page['javascript'] ?? '',
            'attrs'       => json_decode($page['attrs'] ?? '{"title":"Hello World"}', true),
            'json'        => $page['json'] ?? '',
            'template'    => $page['template'] ?? 'index',
        ];

        $page[] = $pageHeadersArray;

        // Segmento
        $AntTpl->set('Segment', Url::segment(0));

        // Comprobamos si esta publicada la pagina
        $page['published'] = ($page['published'] == 'true') ? true : false;

        // Comprobamos que este publicado
        if ($page['published']) {

            // insertamos el array de la pagina
            $AntTpl->set('page', $page);

            // Comprueba si existe una plantilla definida y si no usa index.html
            $themeFile = THEME . '/' . $page['template'] . '.html';

            if (file_exists($themeFile) and is_file($themeFile)) {
                // enseñamos la plantilla
                die($AntTpl->draw($themeFile));
                exit();
            } else {
                // enseñamos la pagina de inicio
                die($AntTpl->draw(THEME . '/index.html'));
                exit();
            }
        } else {
            // insertamos el array de la pagina
            $AntTpl->set('page', $page);
            // enseñamos la pagina de error
            $this->errorPage($AntTpl);
        }
    }
}
