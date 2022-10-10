<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Error
{

    /**
     * Obtiene Error en no publicado..
     *
     * @param object $Tpl
     *
     * @return void
     */
    public function errorPage(
        object $Tpl
    ): void {
        $page = array(
            'title' => self::$config['notPublished']['title'],
            'description' => self::$config['notPublished']['description'],
            'robots' => 'noindex,nofollow',
            'content' => self::$config['notPublished']['content'],
            'tags' => '404',
            'author' => self::$config['author'],
            'image' => '', // href file
            'date' => date('d-m-Y'),
            'keywords' => self::$config['keywords'],
            'category' => 'empty',
            'background' => 'white', // blue, #f55,rgb(0,0,0)
            'video' => '', // src file
            'color' => 'black', // blue, #f55,rgb(0,0,0)
            'css' => '', // src file
            'javascript' => '', // src file
            'attrs' => [1,2,3], // = [1,2,true,'string']
            'json' => '', // = json file
        );
        $Tpl->set('page', $page);
        $Tpl->set('config', self::$config);
        echo $Tpl->draw(THEME . '/404.html');
    }
}
