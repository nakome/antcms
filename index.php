<?php 

require_once __DIR__ . '/app/AntCMS.php';


AntCms::Run()->init(
    __DIR__ . '/app/Config.php',
    __DIR__ . '/app/Templating.php'
);
