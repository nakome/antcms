<?php

declare (strict_types = 1);

$dir = str_replace(DIRECTORY_SEPARATOR, '/', getcwd());

require $dir . '/config/defines.php';
require $dir . '/src/autoload.php';

$templates = require $dir . '/config/templates.php';

Ant\FrontEnd\FrontEnd::Run()->init($templates);
