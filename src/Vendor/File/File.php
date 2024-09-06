<?php

declare (strict_types = 1);

namespace Vendor\File;

use Vendor\Sanitize\Sanitize as Sanitize;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

final class File
{
    /**
     * Obtener archivos
     *
     * @param string $name el nombre del archivo a obtener
     *
     * @return void
     */
    public static function get(string $name): void
    {
        // Verifica si el archivo existe y es un archivo regular
        if (file_exists($name) && is_file($name)) {
            // Imprime el contenido del archivo en el buffer de salida
            echo Sanitize::file($name);
        } else {
            // Si el archivo no existe o no es un archivo regular, lanza una excepción con un mensaje de error
            throw new Exception('No existe el archivo ' . $name);
        }
    }


    /**
     * Escanea los archivos en un directorio y devuelve una lista de archivos que coinciden con el tipo especificado.
     *
     * @param string $folder el directorio a escanear
     * @param string $type el tipo de archivo que se va a buscar
     * @param bool $file_path si se debe devolver el nombre del archivo con la ruta completa o solo el nombre del archivo
     *
     * @return mixed la lista de archivos que coinciden con el tipo especificado o `false` si el directorio no existe
     */
    public static function scan(string $folder, string $type = 'html', bool $file_path = true)
    {
        $data = [];
        if (is_dir($folder)) {
            // Crea un iterador recursivo para recorrer los archivos en el directorio
            foreach ($iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $file) {
                if (null !== $type) {
                    if (is_array($type)) {
                        // Comprueba si el tipo de archivo está en la lista de tipos especificados
                        $file_ext = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($file_ext, $type)) {
                            // Comprueba si el nombre del archivo coincide con el tipo especificado
                            if (strpos($file->getFilename(), $file_ext, 1)) {
                                // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                                if ($file_path) {
                                    $data[] = $file->getPathName();
                                } else {
                                    $data[] = $file->getFilename();
                                }
                            }
                        }
                    } else {
                        // Comprueba si el nombre del archivo coincide con el tipo especificado
                        if (strpos($file->getFilename(), $type, 1)) {
                            // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                            if ($file_path) {
                                $data[] = $file->getPathName();
                            } else {
                                $data[] = $file->getFilename();
                            }
                        }
                    }
                } else {
                    // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                    if ('.' !== $file->getFilename() && '..' !== $file->getFilename()) {
                        if ($file_path) {
                            $data[] = $file->getPathName();
                        } else {
                            $data[] = $file->getFilename();
                        }
                    }
                }
            }
            return $data;
        } else {
            // Si el directorio no existe, devuelve `false`
            return false;
        }
    }
}