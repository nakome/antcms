<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\PasswordHasher\PasswordHasher as PasswordHasher;
use function unlink;

/**
 * Trait FormFunctions - Funciones de formularios
 * -----------------------------
 * authFunctions: funcion de login
 * createFileFunction: funcion para crear archivos
 * ------------------------------
 */
trait FormFunctionsTrait
{
    /**
     * Funcion de login
     *
     * @param string $token Token generado en el view
     * @return void
     */
    public function authFunctions(string $token = "")
    {
        // comprobamos la cookie
        if (array_key_exists('usuario_bloqueado', $_COOKIE)) {
            error_log('toManyAttempts');
            die($this->toManyAttempts());
            exit();
        }

        // botton sign in
        if (array_key_exists('loginAuth', $_POST)) {

            // comprobamos token
            if ($this->checkToken($token) && $this->__client_hash == $this->post('_hash', false)) {

                // comprobamos captcha
                if ($this->post('captcha') == $this->post('_captcha', false)) {
                    return $this->login();
                } else {
                    // Informacion error
                    $redirectUrl = ADMIN_URL . ADMIN_FILE_NAME;
                    $this->msgSet($this->lang('error'), $this->lang('errorMsg'));
                    error_log($this->lang('error') . ' ' . $this->lang('errorMsg'));
                    $this->redirect($redirectUrl);
                }
            } else {
                error_log('CRSF detect');
                die('CRSF detect');
            }
        }
    }

    /**
     * Genera una contraseña y devuelve la contraseña con hash junto con la contraseña original.
     *
     * @return array
     */
    public function generatePasswordFunction(): array
    {
        $output   = '';
        $password = '';
        if (array_key_exists('generate', $_POST)) {
            // Obtenemos el nombre del archivo sin santizar
            $password = $this->post('generatePassword', false);
            $output = password_hash($password,PASSWORD_BCRYPT);
        }
        return [$output, $password];
    }

    /**
     * Borra los archivos temporales de la carpeta tmp/ y redirecciona
     *
     * @return array
     */
    public function removeTempFunction(): void
    {
        $redirectUrl = ADMIN_URL . ADMIN_FILE_NAME . '?get=utilities&name';
        if (array_key_exists('removeTempFiles', $_POST)) {
            // Borramos los archivos temporales de la carpeta temp/
            $folder = PUBLIC_ROOT . '/tmp';

            if (is_dir($folder)) {

                file_exists($folder . '/cache_info.json') && unlink($folder . '/cache_info.json');
                file_exists($folder . '/php-error.log') && unlink($folder . '/php-error.log');

                // Informacion success
                error_log($this->lang('success').''.$this->lang('successRemoveTmp'));
                $this->msgSet($this->lang('success'), $this->lang('successRemoveTmp'));
                $this->redirect($redirectUrl);
            }
            // Informacion error
            error_log($this->lang('error').''. $this->lang('errorRemoveTmp'));
            $this->msgSet($this->lang('error'), $this->lang('errorRemoveTmp'));
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Funcion para crear carpetas
     *
     * @param string $dir
     * @return string
     */
    public function createFolderFunction(string $dir): string
    {
        $currentUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (array_key_exists('createFolder', $_POST)) {
            $name      = $this->post('foldername');
            $current   = $this->post('current');
            $cleanName = $this->cleanName($name);
            // creamos el directorio
            if ($this->createDir($current, $cleanName)) {
                error_log($this->lang('success').''. sprintf($this->lang('folderCreated'), $cleanName));
                $this->msgSet($this->lang('success'), sprintf($this->lang('folderCreated'), $cleanName));
                $this->redirect($currentUrl);
            } else {
                error_log($this->lang('error') . ' ' . sprintf($this->lang('folderNotCreated'), $cleanName));
                $this->msgSet($this->lang('error'), sprintf($this->lang('folderNotCreated'), $cleanName));
                $this->redirect($currentUrl);
            }
        }
        // Formulario
        $form = '<form method="post"><input type="hidden" name="current" value="' . $dir . '"/><div class="mb-3"><label for="foldername" class="form-label">' . $this->lang('folderName') . '</label><input type="text" class="form-control form-control-sm" name="foldername" id="foldername" placeholder="' . $this->lang('folderNameInfo') . '" aria-describedby="createFolderHelp" required/><div id="createFolderHelp" class="form-text">' . $this->lang('createFolderHelp') . '</div></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="createFolder" value="' . $this->lang('save') . '"></div></form>';
        return $form;
    }

    /**
     * Funcion para crear archivos
     *
     * @param string $dir
     * @return string
     */
    public function createFileFunction(string $dir): string
    {
        $currentUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (array_key_exists('createFile', $_POST)) {
            $name      = $this->post('filename');
            $current   = $this->post('current');
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename  = pathinfo($name, PATHINFO_FILENAME);
            $cleanName = $this->cleanName($filename);
            // creamos el directorio
            if ($this->createFile($current, $cleanName . '.' . $extension)) {
                error_log($this->lang('success').''. sprintf($this->lang('fileCreated'), $cleanName));
                $this->msgSet($this->lang('success'), sprintf($this->lang('fileCreated'), $cleanName));
                $this->redirect($currentUrl);
            } else {
                error_log($this->lang('error') . ' ' . sprintf($this->lang('fileNotCreated'), $cleanName));
                $this->msgSet($this->lang('error'), sprintf($this->lang('fileNotCreated'), $cleanName));
                $this->redirect($currentUrl);
            }
        }
        // Formulario
        $form = '<form method="post"><input type="hidden" name="current" value="' . $dir . '"/><div class="mb-3"><label for="filename" class="form-label">' . $this->lang('fileName') . '</label><input type="text" class="form-control form-control-sm" name="filename" id="filename" aria-describedby="folderInfileNameInfofo" placeholder="' . $this->lang('fileNameInfo') . '.html"  required/><div id="folderInfoBlock" class="form-text">' . $this->lang('createFileInfo') . '</div></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="createFile" value="' . $this->lang('save') . '"></div></form>';
        return $form;
    }

    /**
     * Funcion para subir archivos
     *
     * @param string $dir
     * @return string
     */
    public function uploadFunction(string $dir): string
    {
        // Agregamos funciones
        $this->uploadFiles($dir);
        // Formulario
        $form = '<form method="post"enctype="multipart/form-data" id="upload-form"><div class="mb-3"><input type="file" aria-describedby="uploadInfoHelp" id="file-input" class="form-control form-control-sm" name="files[]" multiple directory="false" required accept="image/*,video/mp4,video/ogg,video/webm,audio/mp3,audio/wav,audio/ogg,audio/aac,.php,text/*,application/*" /><div id="uploadInfoHelp" class="form-text">' . $this->lang('uploadInfoHelp') . '</div></div><div class="btn-group"><input type="submit" id="upload-button" class="btn btn-sm btn-outline-dark rounded-0" value="' . $this->lang('save') . '"></div></form>';
        return $form;
    }

    /**
     * Funcion para editar
     *
     * @param string $route
     * @return void
     */
    public function editFunctions(string $route = '')
    {
        if (array_key_exists('update', $_POST)) {
            // Obtenemos el nombre del archivo sin santizar
            $filename = $this->post('filename', false);
            // Removemos PUBLIC_ROOT de la ruta
            $output        = str_replace(PUBLIC_ROOT, '', $route);
            $redirectRoute = '?edit&name=' . base64_encode($output);
            if ($this->saveContent($route, $filename)) {
                error_log($this->lang('success').''. sprintf($this->lang('fileUpdated'), $output));
                $this->msgSet($this->lang('success'), sprintf($this->lang('fileUpdated'), $output));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME . $redirectRoute);
            } else {
                error_log($this->lang('error') . ' ' . sprintf($this->lang('fileNotUpdated'), $output));
                $this->msgSet($this->lang('error'), sprintf($this->lang('fileNotUpdated'), $output));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME . $redirectRoute);
            }
        }
    }

    /**
     * Funcion para renombrar
     *
     * @param string $route
     * @return void
     */
    public function renameFunctions(string $route = '')
    {
        if (array_key_exists('rename', $_POST)) {

            // Obtenemos el nombre del archivo
            $filename = $this->cleanName($this->post('filename'));

            // current
            $extension = pathinfo($route, PATHINFO_EXTENSION);

            // La ruta y el nuevo nombre
            $newfilename = dirname($route) . '/' . $filename . '.' . $extension;

            // Si el archivo ya existe imprimimos mensaje y redireccionamos
            if (file_exists($newfilename) && is_file($newfilename)) {
                $output = str_replace(PUBLIC_ROOT, '', $route);
                error_log($this->lang('error').''. sprintf($this->lang('fileRenamedExists'), $newfilename));
                $this->msgSet($this->lang('error'), sprintf($this->lang('fileRenamedExists'), $newfilename));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME . '?rename&name=' . base64_encode($output));
            }

            // renombramos el archivo
            if (rename($route, $newfilename)) {
                error_log($this->lang('success').''. sprintf($this->lang('fileRenamed'), $newfilename));
                $this->msgSet($this->lang('success'), sprintf($this->lang('fileRenamed'), $newfilename));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
            } else {
                error_log($this->lang('error') . ' ' . sprintf($this->lang('fileNotRenamed'), $newfilename));
                $this->msgSet($this->lang('error'), sprintf($this->lang('fileNotRenamed'), $newfilename));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
            }
        }
    }
}
