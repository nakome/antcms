<?php

declare (strict_types = 1);

/*
 * Acceso restringido
 */
defined('SECURE') or die('No tiene acceso al script.');

return [
    'url' => 'http://localhost/gitlab/antcms',
    // THEME CONFIG
    // ----------------------------
    'google-site-verification' => '',
    // color tema
    'theme_color' => '#0d6efd',
    // background color
    'background_color' => '#fff',
    // orientation
    'orientation' => 'portrait',
    // display
    'display' => 'standalone',
    // shortname
    'short_name' => 'AntCMS',
    // lenguaje
    'lang' => 'es',
    // charset
    'charset' => 'UTF-8',
    // timezone
    'timezone' => 'Europe/Brussels',
    // titulo de la web
    'title' => 'AntCMS',
    // descripcion de la web
    'description' => 'Flat File AntCMS',
    // palabras clave
    'keywords' => 'desarrollo,web,cms,php',
    // autor
    'author' => 'Moncho Varela',
    // correo
    'email' => 'nakome@demo.com',
    // imagen por defecto
    'default_image' => 'public/notfound.jpg',
    // paginación por página
    'pagination' => 4,
    // Derechos de autor
    'copyright' => 'AntCMS',
    // navegacion
    'menu' => [
        '/' => 'Inicio',
        '/documentacion' => 'Documentación',
        '/blog' => 'Blog',
        '/contact' => 'Contacto',
        '/asdfdsaf' => 'Error'
    ],
    // Página no publicada
    'notPublished' => [
        'title' => 'Página no publicada',
        'description' => 'La página a la que esta accediendo aún no se ha publicado o se ha desactivado',
        'content' => '<div class="bg-light p-5 shadow"><h1 class="text-danger">Página no publicada</h1><p class="lead">La página a la que esta accediendo aún no se ha publicado o se ha desactivado.</p></div>',
    ],
];
