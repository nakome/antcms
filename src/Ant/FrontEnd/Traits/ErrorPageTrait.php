<?php

declare (strict_types = 1);

namespace Ant\FrontEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

trait ErrorPageTrait
{

    /**
     * Obtiene Error en no publicado..
     *
     * @param object $Tpl
     *
     * @return void
     */
    public function errorPage(object $Tpl): void
    {
        $page = array(
            'title' => NOT_PUBLISHED['title'],
            'description' => NOT_PUBLISHED['description'],
            'robots' => 'noindex,nofollow',
            'content' => NOT_PUBLISHED['content'],
            'tags' => '404',
            'author' => AUTHOR,
            'image' => '', // href file
            'date' => date('d-m-Y'),
            'keywords' => SITE_KEYWORDS,
            'category' => 'empty',
            'background' => 'white', // blue, #f55,rgb(0,0,0)
            'video' => '', // src file
            'color' => 'black', // blue, #f55,rgb(0,0,0)
            'css' => '', // src file
            'javascript' => '', // src file
            'attrs' => [1, 2, 3], // = [1,2,true,'string']
            'json' => '', // = json file
        );
        $Tpl->set('page', $page);
        echo $Tpl->draw(THEME . '/404.html');
    }
}