<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

Trait File
{

    /**
     * Obtener archivos
     * 
     * @param string $name
     * 
     * @return void 
     */
    public static function getFile(string $name): void
    {
        if (file_exists($name) && is_file($name)) {
            echo file_get_contents($name);
        } else {
            throw new Exception('No existe el archivo ' . $name);
        }
    }
}