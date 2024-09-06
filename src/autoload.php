<?php

declare (strict_types = 1);

namespace Src;

defined('ACCESS') or die('No script access here.');

spl_autoload_register(function ($className) {
    include_once dirname(__DIR__) . '/src/' . str_replace("\\", "/", $className . '.php');
});
