<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Exception;

/**
 * Trait FileManager - Manejo de archivos
 * -----------------------------
 * uploadFiles: funcion para subir archivos
 * removeFile: funcion para borrar archivos
 * removeDir: funcion para borrar directorios
 * createDir: funcion para crear directorios
 * createFile: funcion para crear archivos
 * saveContent: funcion para guardar contenido
 * moveFiles: funcion para mover archivos
 * moveDir: funcion para mover directorios
 * getDirInfo: funcion para obtener informacion de directorios
 * getFileInfo: funcion para obtener informacion de archivos
 * ------------------------------
 */
trait FileManagerTrait
{

    /**
     * Subir archivos
     *
     * @param string $dir directorio para subir archivos
     * @return void
     */
    public function uploadFiles(string $dir = "")
    {

        $currentUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Comprobamos si se ha enviado un archivo
        if (isset($_FILES['files'])) {

            $file = $_FILES['files'];
            $totalFiles = count($file['name']); // Obtenemos el número total de archivos a subir
            $uploadedFiles = 0; // Inicializamos el contador de archivos subidos a cero
            $maxFileSize = 100 * 1024 * 1024; // Tamaño máximo permitido en bytes (30 MB)

            // Iteramos sobre cada archivo
            for ($i = 0; $i < $totalFiles; $i++) {

                $filename = $file['name'][$i]; // Obtenemos el nombre del archivo
                $tmpname = $file['tmp_name'][$i]; // Obtenemos la ruta temporal donde se ha guardado el archivo
                $size = $file['size'][$i];

                // Si algún archivo excede el tamaño máximo, muestra un mensaje de error y detiene la ejecución del script.
                if ($size > $maxFileSize) {
                    $this->msgSet($this->lang('error'), $this->lang('errorFileSize'));
                    $this->redirect($currentUrl);
                }
                // Carpeta de destino
                $destination = $dir . '/' . basename($filename);
                // Movemos el archivo al directorio y contamos
                if (move_uploaded_file($tmpname, $destination)) {
                    // Incrementamos el contador de archivos subidos
                    $uploadedFiles++;
                }

            }
            // Redirigimos a la página de destino con un mensaje de éxito o fracaso
            if ($uploadedFiles == $totalFiles) {
                $this->msgSet($this->lang('success'), $this->lang('uploadSuccess'));
                $this->redirect($currentUrl);
            } else {
                error_log($this->lang('uploadError'));
                $this->msgSet($this->lang('error'), $this->lang('uploadError'));
                $this->redirect($currentUrl);
            }
        }
    }

    /**
     * Función para borrar archivos
     *
     * @param string $filename
     * @return boolean
     */
    public function removeFile(string $filename = ""): bool
    {
        // Comprobamos que es un archivo
        if (file_exists($filename) && is_file($filename)) {

            // Intenta borrar el archivo
            if (unlink($filename)) {
                // Retornamos true si existe
                return (!file_exists($filename)) ? true : false;
            }
        }
    }

    /**
     * Crea un archivo en el directorio especificado
     *
     * @param string $dir Directorio donde crear el archivo
     * @param string $name Nombre del archivo
     * @return bool true si se crea correctamente, false en caso contrario
     */
    public function createFile(string $dir = "", string $name = ""): bool
    {
        $folderName = $dir . '/' . $name;
        if (!file_exists($folderName)) {
            // Obtenemos la extension
            $extension = pathinfo($folderName, PATHINFO_EXTENSION);
            // Comprobamos que lleva extension
            if ($extension) {
                $archivo = fopen($folderName, "w") or die($this->lang('noCreateFile'));
                $texto = $this->lang('createFileSuccess');
                fwrite($archivo, $texto);
                fclose($archivo);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Función que permite guardar el contenido de un archivo
     *
     * @param string $filename
     * @param string $data
     * @return boolean
     */
    public function saveContent(string $filename = "", string $data = ""): bool
    {
        // Comprobamos que es un archivo
        if (file_exists($filename) && is_file($filename)) {

            // Guardamos
            file_put_contents($filename, $data);

            // Si se guarda bien enviamos mensaje y redirigimos al mismo sitio
            return (file_get_contents($filename) == $data) ? true : false;
        }
        return false;
    }

    /**
     * Función que permite mover un archivo de una ubicación a otra.
     * @param string $filename El nombre del archivo a mover.
     * @param string $fileRouteIn La ruta actual del archivo.
     * @param string $fileRouteOut La ruta donde se desea mover el archivo.
     * @return void
     */
    public function moveFiles(string $filename = "", string $fileRouteIn = "", string $fileRouteOut = "")
    {
        // Se construye la ruta del archivo actual.
        $actualFileRoute = PUBLIC_ROOT . '/' . $fileRouteIn . '/' . $filename;

        // Verifica si el archivo existe y es un archivo válido.
        if (file_exists($actualFileRoute) && is_file($actualFileRoute)) {

            // Directorio donde se va a mover el archivo
            $outputFile = PUBLIC_ROOT . '/' . $fileRouteOut . '/' . $filename;

            // Intenta mover el archivo a la nueva ubicación.
            $result = rename($actualFileRoute, $outputFile);

            // Si se logra mover el archivo, se envía un mensaje de éxito y se redirecciona a la nueva carpeta.
            if ($result) {
                $this->msgSet($this->lang('success'), $this->lang(sprintf('moveFileSuccess', addslashes($filename))));
                $this->redirect(ADMIN_URL . '?get=dir&name=' . base64_encode(dirname($outputFile)));
            } else {
                // Si no se logra mover el archivo, se envía un mensaje de error y se redirecciona a la nueva carpeta.
                error_log($this->lang('moveFileError'));
                $this->msgSet($this->lang('error'), $this->lang(sprintf('moveFileError', addslashes($filename))));
                $this->redirect(ADMIN_URL . '?get=dir&name=' . base64_encode(dirname($outputFile)));
            }
        }
    }

    /**
     * Obtener las carpetas y archivos
     *
     * @param [type] $dir
     * @return array
     */
    public function getDirInfo(string $dir): array
    {
        // Verificar si la ruta es un directorio
        if (is_dir($dir)) {

            // Si la ruta es un directorio, abrimos el directorio
            if ($dh = opendir($dir)) {

                // Creamos un arreglo vacío para almacenar la información de los archivos y directorios
                $result = [];

                // Obtenemos la ruta del directorio raíz
                $PUBLIC_ROOT = str_replace(PUBLIC_ROOT, '', $dir);

                // Leemos el directorio
                while (($file = readdir($dh)) !== false) {

                    // No enseñar esto: Saltar archivos ocultos como .htaccess, .git y .gitignore que hay en la opcion exclude
                    if (in_array(basename($file), EXCLUDE_DIRECTORIES)) {
                        continue;
                    }

                    // Si el archivo no es un archivo oculto
                    if ($file != '.' && $file != '..') {

                        // Si el archivo es un directorio
                        if (is_dir($dir . '/' . $file)) {

                            // Agregamos información sobre el directorio al arreglo de resultados
                            $result[] = [
                                'filepath' => $PUBLIC_ROOT . '/' . $file,
                                'filename' => $file,
                                'filetype' => 'dir',
                                'fileext' => false,
                            ];
                        } else {
                            // Si el archivo no es un directorio, obtenemos información adicional sobre el archivo
                            $file_info = pathinfo($PUBLIC_ROOT . '/' . $file);

                            // Si el archivo tiene una extensión
                            if (isset($file_info['extension'])) {

                                // Obtenemos la extensión del archivo
                                $file_info = pathinfo($PUBLIC_ROOT . '/' . $file);
                                $file_extension = $file_info['extension'];

                                // Agregamos información sobre el archivo al arreglo de resultados
                                $result[] = [
                                    'filepath' => $PUBLIC_ROOT . '/' . $file,
                                    'filename' => $file,
                                    'filetype' => 'file',
                                    'fileext' => $file_extension,
                                ];
                            } else {
                                // Si el archivo no tiene una extensión, lo tratamos como un archivo de código
                                $result[] = [
                                    'filepath' => $PUBLIC_ROOT . '/' . $file,
                                    'filename' => $file,
                                    'filetype' => 'file',
                                    'fileext' => "code",
                                ];
                            }
                        }
                    }
                }

                // Cerramos el directorio
                closedir($dh);

                // Devolvemos el arreglo de resultados
                return $result;
            }
        }
        // Si la ruta no es un directorio, devolvemos un array vacio
        return [];
    }

    /**
     * Obtener la información del archivo
     *
     * @param string $filename Ruta y nombre del archivo a obtener información
     * @return array Array con información del archivo o array vacio si no se puede obtener la información
     */
    public function getFileInfo(string $filename = ""): array
    {
        if (is_dir($filename) || is_file($filename)) {

            // Obtenemos el tamaño del archivo en bytes
            $filesize = filesize($filename);

            // Obtenemos la fecha de modificación del archivo en formato Unix timestamp
            $filedate = filemtime($filename);

            // Obtenemos los permisos del archivo en octal
            $fileperms = fileperms($filename);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            // Devolvemos un array con la información del archivo
            return [
                'filepath' => $filename, // Ruta y nombre del archivo
                'fileinfo' => pathinfo($filename), // Información del archivo (nombre, extensión, directorio, etc.)
                'fileperms' => decoct($fileperms&0777), // Permisos del archivo en octal
                'filesize' => $filesize, // Tamaño del archivo en bytes
                'filedate' => date("d-m-Y H:i:s", $filedate), // Fecha de modificación del archivo en formato humano
            ];
        }
        return [];
    }

    /**
     * Crea una carpeta en el directorio especificado
     *
     * @param string $dir Directorio donde crear la carpeta
     * @param string $name Nombre de la carpeta
     * @return bool true si se crea correctamente, false en caso contrario
     */
    public function createDir(string $dir = "", string $name = ""): bool
    {
        $folderName = $dir . '/' . $name;
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina un directorio y su contenido de forma recursiva.
     *
     * @param string $directorio La ruta del directorio que se eliminará.
     * @return int El número de archivos y directorios eliminados con éxito.
     * @throws Exception Si no se puede leer el directorio o si se producen errores al eliminar el directorio.
     */
    public function removeDir(string $dir = ""): int
    {
        // Verifica si el directorio es legible
        if (!is_readable($dir)) {
            throw new Exception($this->lang('cantReadDir') . ": $dir");
        }
        // Contadores para el número de archivos y directorios eliminados con éxito y errores
        $success = 0;
        $fail = 0;

        // Obtiene una lista de archivos y directorios en el directorio, excluyendo "." y ".."
        $files = array_diff(scandir($dir), array('.', '..'));
        // Itera a través de cada archivo y directorio en el directorio
        foreach ($files as $file) {

            // Construye la ruta completa del archivo o directorio
            $filedir = $dir . DIRECTORY_SEPARATOR . $file;

            // Si el archivo es un directorio, llama a la función $this->removeDir() de forma recursiva
            if (is_dir($filedir)) {
                try {
                    $this->removeDir($filedir);
                    $success++;
                } catch (Exception $e) {
                    // Si se produce un error, aumenta el contador de errores
                    $fail++;
                }
            } else {
                // Si el archivo es un archivo, intenta eliminarlo
                if (unlink($filedir)) {
                    $success++;
                } else {
                    // Si se produce un error, aumenta el contador de errores
                    $fail++;
                }
            }
        }
        // Intenta eliminar el directorio
        if (rmdir($dir)) {
            $success++;
        } else {
            // Si se produce un error, aumenta el contador de errores
            $fail++;
        }
        // Si se produjeron errores, lanza una excepción
        if ($fail > 0) {
            throw new Exception($this->lang('errorsOnDelete') . ": $dir");
        }
        // Devuelve el número de archivos y directorios eliminados con éxito
        return $success;
    }

    /**
     * Obtener el tamaño del archivo o directorio en bytes
     * @param  [string] $fileOrDirectory
     * @return [int|bool]
     */
    public static function getFileSize($fileOrDirectory)
    {
        // Files
        if (is_file($fileOrDirectory)) {
            return filesize($fileOrDirectory);
        }
        // Directories
        if (file_exists($fileOrDirectory)) {
            $size = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fileOrDirectory, FilesystemIterator::SKIP_DOTS)) as $file) {
                try {
                    $size += $file->getSize();
                } catch (Exception $e) {
                    // SplFileInfo::getSize RuntimeException will be thrown on broken symlinks/errors
                    error_log($e->getMessage());
                }
            }
            return $size;
        }
        return 0;
    }
}
