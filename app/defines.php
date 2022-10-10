<?php

defined('SECURE') or die('No tiene acceso al script.');
define('ANTCMS_MINIMUM_PHP', '7.4.0');
define('CONFIG', ROOT . '/app/antcms/config');
define('THEME', ROOT . '/public/views');
define('CONTENT', ROOT . '/public/content');
define('DEBUG', true);

// Mostrar errores si dev_mode es true
if (DEBUG) {
    ini_set('display_errors', (string) 1);
    ini_set('display_startup_errors', (string) 1);
    ini_set('track_errors', (string) 1);
    ini_set('html_errors', (string) 1);
    error_reporting(E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set('display_errors', (string) 0);
    ini_set('display_startup_errors', (string) 0);
    ini_set('track_errors', (string) 0);
    ini_set('html_errors', (string) 0);
    error_reporting(0);
}

// Dar formato a la fecha
setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
header("X-Powered-By: AntCMS");
if (version_compare($ver = PHP_VERSION, $req = ANTCMS_MINIMUM_PHP, '<')) {
    $out = sprintf('Usted esta usando PHP %s, pero AntCMs necesita <strong>PHP %s</strong> para funcionar.', $ver, $req);
    exit($out);
}

