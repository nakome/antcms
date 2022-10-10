<?php

declare (strict_types = 1);

define('ROOT', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));
define('SECURE', true);

require_once __DIR__ . '/defines.php';
require_once __DIR__ . '/anttpl/AntTPL.php';
require_once __DIR__ . '/antcms/AntCMS.php';

AntCms\AntCMS::Run()->init(
    __DIR__ . '/config/default.php',
    __DIR__ . '/config/templating.php'
);
