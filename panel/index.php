<?php

declare (strict_types = 1);

use Ant\BackEnd\BackEnd as BackEnd;

// Carga las variables de configuración de ../
$dir = str_replace(DIRECTORY_SEPARATOR, '/', dirname(getcwd()));

require $dir . '/config/defines.php';
require $dir . '/src/autoload.php';

$lang    = require $dir . '/config/lang.php';

$app = new BackEnd($lang);
$app->init();
