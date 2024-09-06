<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait HtmlViewManager - Generar vista HTML
 * -----------------------------
 * displayLoginView: funcion que genera la vista de el login
 * displaySidebarView: funcion que genera la vista del sidebar
 * displayDefaultView: funcion que genera la vista predeterminada
 * displayPlainView : funcion que genera la vista de texto plano
 * modalView: funcion que genera la vista de modal
 * btnModalView: funcion que genera el boton de modal
 * displayDropdownView: funcion que genera el dropdown
 * dropdownLinksView: funcion que genera los enlaces del dropdown
 * displayEditView: funcion que genera la vista de edit
 * displayRenameView: funcion que genera la vista de rename
 * displayDeleteView: funcion que genera la vista de delete
 * ------------------------------
 */
trait HtmlViewManagerTrait
{

    /**
     * Crear ventana modal
     *
     * @param string $id
     * @param string $title
     * @param string $content
     * @return string
     */
    public function displayModalView(string $id, string $title, string $content = ''): string
    {
        return '<div class="modal fade" id="' . $id . '" tabindex="-1" aria-labelledby="' . $id . 'Label" aria-hidden="true"><div class="modal-dialog"><div class="modal-content rounded-0"><div class="modal-header p-2 px-3"><h3 class="modal-title fs-5" id="' . $id . 'Label">' . $title . '</h3><button type="button" class="btn-sm btn-close" data-bs-dismiss="modal" aria-label="Close" title="Close"></button></div><div class="modal-body bg-light">' . $content . '</div></div></div></div>';
    }

    /**
     * Crear html btn modal
     *
     * @param string $icon
     * @param string $title
     * @param string $target
     * @param string $color
     * @return string
     */
    public function displayBtnModalView(string $icon, string $title, string $target, string $color = 'dark', string $me = '1'): string
    {
        return '<a class="btn btn-sm btn-' . $color . ' rounded-0 me-1 mb-2" data-bs-toggle="modal" data-bs-target="#' . $target . '" title="' . $title . '">' . $icon . '' . $title . '</a>';
    }

    /**
     * Generar una vista desplegable con los enlaces dados.
     *
     * @param array $links La matriz de enlaces que se mostrarán en la vista desplegable
     * @return string El HTML de la vista desplegable
     */
    public function displayDropdownView(array $links = []): string
    {
        $dropdownItems = '';

        foreach ($links as $link) {
            $isTarget        = isset($link['target']) && $link['target'] !== null;
            $targetAttribute = $isTarget ? 'target="' . $link['target'] . '"' : '';

            if ($link['name'] === 'divider') {
                $dropdownItems .= '<li><hr class="dropdown-divider"/></li>';
            } else {
                $dropdownItems .= '<li><a ' . $targetAttribute . ' class="' . $link['class'] . '" href="' . $link['link'] . '">' . $link['name'] . '</a></li>';
            }
        }

        return '<div class="dropdown d-flex justify-content-center"><button class="btn btn-white no-caret btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Dropdown">⋮</button><ul class="dropdown-menu p-0 fs-6">' . $dropdownItems . '</ul></div>';
    }

    /**
     * Genera enlaces desplegables en función del tipo y la ruta del archivo
     *
     * @param string $type
     * @param string $filePath
     * @return array
     */
    public function dropdownLinksView(string $type, string $filePath): array
    {
        $url = ADMIN_URL . ADMIN_FILE_NAME;

        $dropdownLinks = [];
        switch ($type) {
            case 'dir':
                $dropdownLinks = [
                    [
                        'name'  => $this->lang('open'),
                        'link'  => $url . '?get=dir&name=' . $filePath,
                        'class' => 'dropdown-item',
                    ],
                    [
                        'name' => 'divider',
                        'link' => false,
                    ],
                    [
                        'name'  => $this->lang('delete'),
                        'link'  => $url . '?delete&name=' . $filePath,
                        'class' => 'dropdown-item text-danger',
                    ],
                ];
                break;
            case 'file':

                $realLink   = $this->createRealLink(base64_decode($filePath));
                $isEditable = $this->isFileEditable(base64_decode($filePath));

                $dropdownLinks = [
                    [
                        'name'   => $this->lang('raw'),
                        'link'   => $url . '?get=file&name=' . $filePath,
                        'target' => '_blank',
                        'class'  => 'dropdown-item text-muted',
                    ],
                    [
                        'name'  => $isEditable ? $this->lang('edit') : $this->lang('view'),
                        'link'  => $isEditable ? $url . '?edit&name=' . $filePath : $realLink,
                        'class' => 'dropdown-item',
                    ],
                    [
                        'name'  => $this->lang('rename'),
                        'link'  => $url . '?rename&name=' . $filePath,
                        'class' => 'dropdown-item',
                    ],
                    [
                        'name' => 'divider',
                        'link' => false,
                    ],
                    [
                        'name'  => $this->lang('delete'),
                        'link'  => $url . '?delete&name=' . $filePath,
                        'class' => 'dropdown-item text-danger',
                    ],
                ];
                break;
        }

        return $dropdownLinks;
    }

    /**
     * Creamos la vista de el login
     *
     * @param string $dir
     * @return string
     */
    public function displayLoginView(string $dir = ""): string
    {
        // url base
        $url = ADMIN_URL;
        // title
        $title = SITE_TITLE;
        $logo  = LOGO;

        // Captcha
        $captcha = $this->captchaToken(6, '123456790');

        // Token
        $token = $this->generateToken();

        // client hash
        $client_hash = $this->__client_hash;

        // Funciones del login
        $this->authFunctions($token);

        // html
        $html = '<div class="row"><div class="col-12 col-sm-6 col-md-4 col-xl-3 col-xxl-3 m-auto"><header class="mb-3 my-5"> <img class="rounded-pill me-2" src="' . $logo . '" alt="logo"><span>' . $title . '</span></header><form method="post"> <input type="hidden" name="_captcha" value="' . $captcha . '"/> <input type="hidden" name="_hash" value="' . $client_hash . '"/><div class="mb-3"><label for="password" class="form-label">' . $this->lang('password') . '</label> <input type="password" class="form-control form-control-sm" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\S]{8,}$" placeholder="**********" autocomplete="current-password" required></div><div class="mb-3"><label for="catpcha" class="form-label">' . $this->lang('writecaptchanumber') . ' <span class="text-danger">' . $captcha . '</span> </label><input type="number" class="form-control form-control-sm" name="captcha" title="captcha" required> </div><div class="mb-3"><button type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="loginAuth">🔓<span>' . $this->lang('enter') . '</span></button></div></form></div></div>';

        // generamos el layout
        return $this->viewLayout($html, '', '', $dir, false);
    }

    /**
     * Creamos el sidebar
     *
     * @return string
     */
    public function displaySidebarView(): string
    {
        // Generamos la lista de las carpetas
        $url = ADMIN_URL . ADMIN_FILE_NAME;

        // Contenido de las carpetas publicas
        $folders = $this->getDirInfo(PUBLIC_ROOT . '/public');

        // Ordenamos alfabéticamente las carpetas por su nombre ('filename')
        usort($folders, function ($a, $b) {
            return strcmp($a['filename'], $b['filename']);
        });

        $list = '';

        // Recorremos el contenido de las carpetas
        foreach ($folders as $folder) {
            // Obtenemos la ruta de la carpeta en base64 para mostrarlo
            $filePath = base64_encode($folder['filepath']);

            // Obtenemos el enlace de la carpeta o archivo
            $list .= '<a href="' . $url . '?get=dir&name=' . $filePath . '" class="list-group-item list-group-item-action">📂 ' . ucfirst($folder['filename']) . '</a>';
        }

        return $list;
    }

    /**
     * Generar una vista de tabla.
     *
     * @param array $contentFolder La matriz que contiene el contenido de la carpeta
     * @return string The HTML tabla que representa el contenido de la carpeta
     */
    public function displayTableView(array $contentFolder = []): string
    {
        // Generamos la lista de las carpetas
        $url = ADMIN_URL . ADMIN_FILE_NAME;

        // Generamos la tabla de los archivos
        $table = '<table class="table table-bordered">';
        $table .= '<tr class="bg-light"><th class="text-dark text-truncate" scope="col">' . $this->lang('files') . '</th><th class="text-dark text-truncate" scope="col">' . $this->lang('perms') . '</th><th  class="text-dark text-truncate d-none d-md-table-cell" scope="col">' . $this->lang('size') . '</th><th class="text-dark text-truncate d-none d-lg-table-cell" scope="col">' . $this->lang('date') . '</th><th class="text-dark text-truncate" scope="col">' . $this->lang('extension') . '</th><th class="text-dark" scope="col">' . $this->lang('options') . '</th></tr>';

        // Recorremos el contenido de los archivos
        foreach ($contentFolder as $file) {

            // Obtenemos la ruta del archivo en base64 para mostrarlo
            $filePath = base64_encode($file['filepath']);

            // Obtenemos el icono correspondiente
            $icon = ($file['filetype'] === 'dir') ? '📁' : '📄';

            $realLink = $this->createRealLink($file['filepath']);

            // Obtenemos la url del archivo para mostrarlo
            $link = ($file['filetype'] === 'dir') ? $url . '?get=dir&name=' . $filePath : $realLink;

            $ext = 'text-danger';
            if ($file['fileext'] === 'php' || $file['fileext'] === 'png' || $file['fileext'] === 'jpg'):
                $ext = 'text-primary';
            elseif ($file['fileext'] === 'js' || $file['fileext'] === 'pdf' || $file['fileext'] === 'json'):
                $ext = 'text-success';
            elseif ($file['fileext'] === 'css' || $file['fileext'] === 'mp4' || $file['fileext'] === 'mp3'):
                $ext = 'text-info';
            endif;

            $capitalizeName = ($file['filetype'] === 'dir') ? '<span class="text-dark">' . ucfirst($file['filename']) . '</span>' : '<span class="' . $ext . '">' . ucfirst($file['filename']) . '</span>';

            // Obtenemos el nombre del archivo para mostrarlo
            $filename = ($file['filetype'] === 'dir') ? '<a class="text-decoration-none link-dark" href="' . $link . '">' . $capitalizeName . ' </a>' : '<a rel="noopener" target="_blank"class="text-decoration-none link-dark" href="' . $link . '">' . $capitalizeName . ' </a>';

            // Obtenemos la información del archivo
            $fileInfo = $this->getFileInfo(PUBLIC_ROOT . '/' . $file['filepath']);

            // Obtenemos el tamaño del archivo
            $size = ($file['filetype'] === 'dir') ? $this->formatFileSize($this->getFileSize(PUBLIC_ROOT . '/' . $file['filepath'])) : $this->formatFileSize($fileInfo['filesize']);

            // Obtenemos los permisos del archivo
            $perms = $fileInfo['fileperms'];

            // Obtenemos la fecha de modificación
            $date      = $fileInfo['filedate'];
            $extension = ($file['filetype'] === 'dir') ? '<span class="badge bg-light text-muted">' . $this->lang('directory') . '</span>' : '<span class="badge bg-light ' . $ext . '">' . $fileInfo['fileinfo']['extension'] . '</span>';

            // Generamos el dropdown de enlaces
            $dropdownLinks = $this->dropdownLinksView($file['filetype'], $filePath);

            // Generamos el dropdown de opciones
            $dropdownOptions = $this->displayDropdownView($dropdownLinks);

            // Anadimos el archivo a la tabla de archivos
            $table .= '<tr><td scope="row"><span class="me-2 text-muted">' . $icon . '</span>' . $filename . '</td><td scope="row"><span class="badge bg-light text-dark" style="max-width: 55px;">' . $perms . '</span></td><td  class="d-none d-md-table-cell" scope="row">' . $size . '</td><td scope="row" class="d-none d-lg-table-cell">' . $date . '</td><td scope="row" style="max-width: 65px;">' . $extension . '</td><td  scope="row" style="max-width: 55px;">' . $dropdownOptions . '</td></tr>';
        }

        // Cerramos la tabla
        $table .= '</table>';

        return $table;
    }

    /**
     * Función que devuelve una vista predeterminada para mostrar el contenido de un directorio.
     *
     * @param string $dir Directorio a mostrar.
     * @param array $arr Array opcional con información adicional para mostrar en la vista.
     * @return string HTML con la vista generada.
     */
    public function displayDefaultView(string $dir = PUBLIC_ROOT, array $arr = []): string
    {

        // Cerrar contenedor
        $html = '';
        $list = $this->displaySidebarView();

        // Contenido de los archivos publicos
        $contentFolder = $this->getDirInfo($dir);

        // Generamos la tabla de archivos
        $table = $this->displayTableView($contentFolder);

        // Botones de creación
        $btn1 = $this->displayBtnModalView('📁 ', $this->lang('createFolder'), 'id_create', 'outline-dark');
        $btn2 = $this->displayBtnModalView('📃 ', $this->lang('createFile'), 'id_file', 'outline-dark');
        $btn3 = $this->displayBtnModalView('📤 ', $this->lang('uploadFile'), 'id_upload', 'primary');

        // Modal de opciones
        $modalOptions = $this->displayModalView('id_create', $this->lang('createFolder'), $this->createFolderFunction($dir));
        $modalOptions .= $this->displayModalView('id_file', $this->lang('createFile'), $this->createFileFunction($dir));
        $modalOptions .= $this->displayModalView('id_upload', $this->lang('uploadFile'), $this->uploadFunction($dir));

        // Plantilla de opciones
        $btnOptions = '<div class="row"><div class="col-12">' . $btn1 . $btn2 . $btn3 . '</div></div>';

        // Generamos el layout
        $html .= '<div class="row gx-2"><div class="col-md-3 col-xl-3"><details class="user-select-none border mb-3" open><summary class="p-2 text-muted">' . $this->lang('publicFolder') . '</summary><div class="list-group list-group-flush">' . $list . '</div></details></div><div class="col-md-9 col-xl-9">' . $btnOptions . $table . $modalOptions . '</div></div>';

        // Generamos la plantilla por defecto
        return $this->viewLayout($html, '.dropdown-toggle::after {display: none;}', '', $dir, true);
    }

    /**
     * Obtiene el contenido de un archivo texto plano
     *
     * @param string $dir
     * @return string
     */
    public function displayPlainView(string $name): string
    {
        if (file_exists($name) && is_file($name)) {
            $fileInfo  = pathinfo($name);
            $extension = strtolower($fileInfo['extension']);

            // Tipos de archivo de texto plano
            $textFileExtensions = ['txt', 'html', 'php', 'json', 'xml', 'css', 'js', 'ts', 'scss', 'less'];
            // Tipos de archivo de imagen
            $imageFileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'JPEG', 'JPG', 'PNG', 'GIF', 'webp', 'WEBP'];
            // Tipos de archivo de video
            $videoFileExtensions = ['mp4', 'avi', 'mkv', 'mov', 'webm'];
            // Tipos de archivo de audio
            $audioFileExtensions = ['mp3', 'ogg', 'wav', 'flac'];
            // Tipo de archivo ZIP
            $zipFileExtensions = ['zip'];

            // Verifica el tipo de archivo y maneja según sea necesario
            if (in_array($extension, $textFileExtensions)) {
                header('Content-Type: text/plain');
                return file_get_contents($name);
            } elseif (in_array($extension, $imageFileExtensions)) {
                header('Content-Type: image/' . $extension);
                readfile($name);
            } elseif (in_array($extension, $videoFileExtensions)) {
                header('Content-Type: video/' . $extension);
                readfile($name);
            } elseif (in_array($extension, $audioFileExtensions)) {
                header('Content-Type: audio/' . $extension);
                readfile($name);
            } elseif (in_array($extension, $zipFileExtensions)) {
                // Si es un archivo ZIP, permitir la descarga
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($name) . '"');
                readfile($name);
            } elseif ($extension === 'pdf') {
                header('Content-Type: application/pdf');
                readfile($name);
            } elseif ($extension === 'svg') {
                header('Content-Type: image/svg+xml');
                readfile($name);
            } else {
                return $this->lang('unsupportedFileType');
            }
        } else {
            return $this->lang('fileNotFound');
        }
    }

    /**
     * Display the CodeMirror CSS links.
     *
     * @return string
     */
    public function displayCodeMirrorCssLinks(): string
    {
        // Enlaces Codemirror css
        $cssLinks = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css"
        integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />';
        $cssLinks .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css"
        integrity="sha512-gFMl3u9d0xt3WR8ZeW05MWm3yZ+ZfgsBVXLSOiFz2xeVrZ8Neg0+V1kkRIo9LikyA/T9HuS91kDfc2XWse0K0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />';

        return $cssLinks;
    }

    /**
     * Display CodeMirror JS links.
     *
     * @return string
     */
    public function displayCodeMirrorJsLinks(): string
    {
        // Enlaces Codemirror js
        $jsLinks = '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js?v=1.0.0"
        integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="' . NONCE . '"></script>';
        $jsLinks .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js"
        integrity="sha512-HN6cn6mIWeFJFwRN9yetDAMSh+AK9myHF1X9GlSlKmThaat65342Yw8wL7ITuaJnPioG0SYG09gy0qd5+s777w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="' . NONCE . '"></script>';
        $jsLinks .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js"
        integrity="sha512-rQImvJlBa8MV1Tl1SXR5zD2bWfmgCEIzTieFegGg89AAt7j/NBEe50M5CqYQJnRwtkjKMmuYgHBqtD1Ubbk5ww=="
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="' . NONCE . '"></script>';
        $jsLinks .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js"
        integrity="sha512-I6CdJdruzGtvDyvdO4YsiAq+pkWf2efgd1ZUSK2FnM/u2VuRASPC7GowWQrWyjxCZn6CT89s3ddGI+be0Ak9Fg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="' . NONCE . '"></script>';
        $jsLinks .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js"
        integrity="sha512-LarNmzVokUmcA7aUDtqZ6oTS+YXmUKzpGdm8DxC46A6AHu+PQiYCUlwEGWidjVYMo/QXZMFMIadZtrkfApYp/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" nonce="' . NONCE . '"></script>';

        return $jsLinks;
    }

    /**
     * Editar vista - Funcion para crear vista de editar
     *
     * @param string $route
     * @return string
     */
    public function displayEditView(string $route = ''): string
    {
        // Funciones para el formulario
        $this->editFunctions($route);

        $html = '';
        // Sidebar
        $list = $this->displaySidebarView();
        // Obtenemos la extension del archivo
        $extension = pathinfo($route, PATHINFO_EXTENSION);
        // Comprobamos si es html o Php y usamos htmlspecialchars
        $allowExtensions = ($extension === 'html' || $extension === 'php');
        $routeContent    = $allowExtensions ? htmlspecialchars(file_get_contents($route)) : file_get_contents($route);

        // Plantilla de editar archivo
        $content = '<form method="post"><div class="mb-3"><textarea class="form-control rounded-0 bg-outline-dark text-light" id="filename" name="filename" rows="20" required>' . $routeContent . '</textarea></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="update" value="' . $this->lang('update') . '"/></div></form>';

        // Enlaces Codemirror
        $jsLinks  = $this->displayCodeMirrorJsLinks();
        $cssLinks = $this->displayCodeMirrorCssLinks();

        // Insertamos los enlaces en el layout
        $html .= $jsLinks . $cssLinks;

        // Comprobamos el modo de visualización
        $mode = ($extension === 'php') ? 'php' : (($extension === 'css') ? 'text/css' : (($extension === 'js') ? 'text/javascript' : 'htmlmixed'));

        // Codigo javascript
        $js = <<<JAVASCRIPT
            document.addEventListener('DOMContentLoaded',evt => {
                const file = document.getElementById('filename');
                let editor = CodeMirror.fromTextArea(file, {
                    lineNumbers: true,
                    lineWrapping: true,
                    lineLength: 80,
                    mode: '{$mode}',
                    theme: "dracula",
                });
            })
        JAVASCRIPT;
        // Codigo css
        $css = <<<CSS
        .CodeMirror {
            height: 30rem!important;
        }
        CSS;

        // Generamos el layout
        $html .= '<div class="row gx-2"><div class="col-md-3 col-xl-3"><details class="user-select-none border mb-3" open><summary class="p-2 text-muted">' . $this->lang('publicFolder') . '</summary><div class="list-group list-group-flush">' . $list . '</div></details></div><div class="col-md-9 col-xl-9">' . $content . '</div></div>';
        return $this->viewLayout($html, $css, $js, $route, true);
    }

    /**
     * Renombrar vista - Funcion para crear vista de renombrar
     *
     * @param string $route
     * @return string
     */
    public function displayRenameView(string $route = ''): string
    {
        // Funciones para el formulario
        $this->renameFunctions($route);
        $html = '';
        // Sidebar
        $list = $this->displaySidebarView();
        // Plantilla de editar archivo
        $content = '<div class="row"><div class="col-5"><form method="post"><div class="mb-3"><label for="filename" class="form-label">' . $this->lang('fileName') . '</label><input type="text" class="form-control" id="filename" name="filename" value="' . pathinfo($route, PATHINFO_FILENAME) . '" aria-describedby="renameFileHelp"  required/><div id="renameFileHelp" class="form-text">' . $this->lang('renameFileHelp') . '</div></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="rename" value="' . $this->lang('rename') . '"/></div></form></div></div>';
        // Generamos el layout
        $html .= '<div class="row gx-2"><div class="col-md-3 col-xl-3"><details class="user-select-none border mb-3" open><summary class="p-2 text-muted">' . $this->lang('publicFolder') . '</summary><div class="list-group list-group-flush">' . $list . '</div></details></div><div class="col-md-9 col-xl-9">' . $content . '</div></div>';
        return $this->viewLayout($html, '', '', $route, true);
    }

    /**
     * Delete vista - Funcion para crear vista de renombrar
     *
     * @param string $route
     * @return string
     */
    public function displayDeleteView(string $route = ''): string
    {
        if (array_key_exists('delete', $_POST)) {
            $total = 0;
            // Eliminamos el archivo
            if (is_file($route)) {
                $total = ($this->removeFile($route)) ? 1 : 0;
            }
            // Eliminamos la carpeta
            if (is_dir($route)) {
                $total = $this->removeDir($route);
            }
            // Mostramos el resultado
            if ($total > 0) {
                $output = str_replace(PUBLIC_ROOT, '', $route);
                $this->msgSet($this->lang('error'), sprintf($this->lang('filesDeleted'), $total));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME . '?get=dir&name=' . base64_encode(dirname($output)));
            } else {
                $this->msgSet($this->lang('error'), $this->lang('filesNotDeleted'));
                $this->redirect(ADMIN_URL . ADMIN_FILE_NAME);
            }
        }

        // Sidebar
        $list = $this->displaySidebarView();

        $content = '<div class="row"><div class="col-5"><div class="alert alert-danger">' . sprintf($this->lang('confirmDelete'), pathinfo($route, PATHINFO_BASENAME)) . '</div></div></div><form method="post"><input type="submit" onclick="return confirm(\'' . $this->lang('confirm') . '\')" class="btn btn-sm btn-danger rounded-0" name="delete" value="' . $this->lang('delete') . '"/></form>';

        // Generamos el layout
        $html = '<div class="row gx-2"><div class="col-md-3 col-xl-3"><details class="user-select-none border mb-3" open><summary class="p-2 text-muted">' . $this->lang('publicFolder') . '</summary><div class="list-group list-group-flush">' . $list . '</div></details></div><div class="col-md-9 col-xl-9">' . $content . '</div></div>';
        // Generamos la plantilla por defecto
        return $this->viewLayout($html, '', '', $route, true);
    }

    /**
     * Función para visualizar la vista de utilidades.
     *
     * @return string
     */
    public function displayUtilitiesView(): string
    {
        $html = '';

        // Sidebar
        $list = $this->displaySidebarView();

        // Funciones para el formulario
        list($output, $password) = $this->generatePasswordFunction();

        // Obtenemos el valor de el password y si no esta vacio lo generamos
        $pass   = $password ? $password : $this->generateRandomPassword(15);
        $output = $output ? $output : $this->lang('generatePasswordHash');

        // Metodo para remover la carpeta temporal
        $this->removeTempFunction();

        $viewErrorLog = (file_exists(PUBLIC_ROOT . '/tmp/php-error.log')) ? '<pre class="bg-dark text-light p-2 my-3 h-100 min-h-50 overflow-y-scroll" style="max-height:40rem;pre-wrap">' . file_get_contents(PUBLIC_ROOT . '/tmp/php-error.log') . '</pre>' : '<pre class="bg-dark text-light p-2 my-3 text-wrap h-100 min-h-50 overflow-y-scroll" style="height:20rem"><span class="text-muted">Log info</span></pre>';

        // Plantilla de generar password
        $content = '<div class="col-12 col-md-4"><form method="post"><div class="mb-3"><label for="generatePassword" class="form-label">' . $this->lang('generatePassword') . '</label><input type="text" class="form-control" id="generatePassword" name="generatePassword" value="' . $pass . '" aria-describedby="generatePasswordHelp" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\S]{8,}$"/><div id="generatePasswordHelp" class="form-text">' . $this->lang('generatePasswordHelp') . '</div><pre class="bg-dark text-light p-2 my-3 text-wrap">' . $output . '</pre></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="generate" value="' . $this->lang('generate') . '"/></div></form><form method="post"><div class="mb-3"><pre class="bg-danger text-light p-2 my-3 text-wrap">' . $this->lang('removeTempFiles') . '</pre></div><div class="mb-3"><input type="submit" class="btn btn-sm btn-outline-dark rounded-0" name="removeTempFiles" value="' . $this->lang('remove') . '"/></div></form></div><div class="col-12 col-md-8">' . $viewErrorLog . '</div></div>';

        // Generamos el layout
        $html .= '<div class="row gx-2"><div class="col-md-2 col-xl-3"><details class="user-select-none border mb-3" open><summary class="p-2 text-muted">' . $this->lang('publicFolder') . '</summary><div class="list-group list-group-flush">' . $list . '</div></details></div><div class="col-md-10 col-xl-9"><div class="row">' . $content . '</div></div></div>';
        return $this->viewLayout($html, '', '', '', true);
    }

    /**
     * Mostrar la vista de ayuda de la aplicación.
     *
     * @return string
     */
    public function displayHelpView(): string
    {
        $html = '';
        // Plantilla de editar archivo
        $content = <<<'HTML'
        <h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Archivo de configuración</h3><ul class="list-unstyled"><li><code>&#39;url&#39;</code>: La URL base de tu sitio web.</li><li><code>&#39;google-site-verification&#39;</code>: Código de verificación de Google para la integración con Google Search Console.</li><li><code>&#39;theme_color&#39;</code>: Color principal del tema de tu sitio.</li><li><code>&#39;background_color&#39;</code>: Color de fondo de tu sitio.</li><li><code>&#39;orientation&#39;</code>: Orientación predeterminada de tu sitio.</li><li><code>&#39;display&#39;</code>: Modo de visualización predeterminado de tu sitio.</li><li><code>&#39;short_name&#39;</code>: Nombre corto de tu sitio.</li><li><code>&#39;lang&#39;</code>: Idioma predeterminado de tu sitio.</li><li><code>&#39;charset&#39;</code>: Conjunto de caracteres predeterminado para tu sitio.</li><li><code>&#39;timezone&#39;</code>: Zona horaria predeterminada para tu sitio.</li><li><code>&#39;title&#39;</code>: Título predeterminado de tu sitio.</li><li><code>&#39;description&#39;</code>: Descripción predeterminada de tu sitio.</li><li><code>&#39;keywords&#39;</code>: Palabras clave predeterminadas para tu sitio.</li><li><code>&#39;author&#39;</code>: Autor predeterminado de tu sitio.</li><li><code>&#39;email&#39;</code>: Correo electrónico de contacto predeterminado para tu sitio.</li><li><code>&#39;default_image&#39;</code>: Ruta de la imagen predeterminada para tu sitio.</li><li><code>&#39;pagination&#39;</code>: Número predeterminado de elementos por página para la paginación.</li><li><code>&#39;copyright&#39;</code>: Derechos de autor predeterminados.</li><li><code>&#39;menu&#39;</code>: Menú de navegación de tu sitio, donde la clave es la URL y el valor es el nombre del enlace.</li><li><code>&#39;notPublished&#39;</code>: Configuración para la página que aún no ha sido publicada o está desactivada, incluyendo título, descripción y contenido.</li></ul><p>Estas configuraciones te permiten personalizar diversos aspectos de tu sitio web en AntCMS.</p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Creación de una Página</h3><p>El gestor de contenidos permite la creación de páginas mediante la definición de un archivo HTML con una serie de variables y el contenido deseado. El formato del archivo es el siguiente:</p><pre class="bg-dark text-light p-2 my-3"><code class="language-html">Title: Flat file CMS?<br/>Description: Un sistema de contenido de archivos sin bases de datos.<br/>Published: true<br/>Date: 02/10/2021<br/>Template: index<br/>----<br/>contenido de la pagina<br/></code></pre><h4 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Descripción de las Variables:</h4><ul><li><strong>Title:</strong> Define el título de la página.</li><li><strong>Description:</strong> Describe brevemente el contenido de la página.</li><li><strong>Tags:</strong> Etiquetas asociadas a la página.</li><li><strong>Author:</strong> Nombre del autor de la página.</li><li><strong>Image:</strong> Enlace al archivo de imagen asociado a la página.</li><li><strong>Date:</strong> Fecha de creación o última modificación de la página en formato DD-MM-YY.</li><li><strong>Robots:</strong> Especifica la directiva para la indexación de los motores de búsqueda.</li><li><strong>Keywords:</strong> Palabras clave asociadas a la página.</li><li><strong>Category:</strong> Categoría a la que pertenece la página.</li><li><strong>Template:</strong> Plantilla a utilizar para la visualización de la página, ya sea para la vista de índice o de entrada.</li><li><strong>Published:</strong> Indica si la página está publicada (true) o no (false).</li><li><strong>Background:</strong> Define el color de fondo de la página.</li><li><strong>Video:</strong> Enlace al archivo de video asociado a la página.</li><li><strong>Color:</strong> Define el color principal de la página.</li><li><strong>Css:</strong> Enlace al archivo CSS asociado a la página.</li><li><strong>Javascript:</strong> Enlace al archivo JavaScript asociado a la página.</li><li><strong>Attrs:</strong> Atributos adicionales para la página, especificados en formato de lista.</li><li><strong>Json:</strong> Enlace al archivo JSON asociado a la página.</li></ul><p>Una vez definidas las variables, el contenido de la página se coloca después de la línea de separación (<code>----</code>).</p><p>Por supuesto, podemos agregar el glosario de variables que se utiliza en la plantilla. Aquí está el contenido para ello:</p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Glosario de Variables en la Plantilla</h3><p>La plantilla del gestor de contenidos utiliza una serie de variables especiales para facilitar la manipulación y presentación de datos. A continuación se presenta un glosario de las variables disponibles:</p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Descripción de Variables:</h3><ul><li><strong>{* comentario *}:</strong> Permite incluir comentarios en el código de la plantilla.</li><li><strong>{date}:</strong> Recupera la fecha actual.</li><li><strong>{Year}:</strong> Recupera el año actual.</li><li><strong>{Site_url}:</strong> Obtiene la URL del sitio.</li><li><strong>{Site_current}:</strong> Obtiene el hash del sitio.</li><li><strong>{Pages: nombre}:</strong> Obtiene un listado de páginas.</li><li><strong>{If:}:</strong> Inicia una condición.</li><li><strong>{Else}:</strong> Bloque que se ejecuta si la condición no se cumple.</li><li><strong>{Elseif:}:</strong> Bloque que se ejecuta si otra condición se cumple.</li><li><strong>{/If}:</strong> Cierra una condición.</li><li><strong>{Segment:}:</strong> Inicia un segmento condicional para la URL.</li><li><strong>{/Segment}:</strong> Cierra un segmento condicional.</li><li><strong>{Loop: $datos as $key=&gt;$val}:</strong> Inicia un bucle foreach.</li><li><strong>{/Loop}:</strong> Cierra un bucle foreach.</li><li><strong>{? $var = &#39;nueva variable&#39; ?}:</strong> Crea una nueva variable.</li><li><strong>{?= $var?} o {$var}:</strong> Llama a una variable existente.</li><li><strong>{$page.title}:</strong> Obtiene variables específicas de la página, como el título y la descripción.</li><li><strong>{$config.title}:</strong> Obtiene variables del archivo de configuración, como el título y la descripción etc...</li><li><strong>{$page.title|capitalize}:</strong> Esta variable toma el título de la página y lo convierte en mayúscula solo para la primera letra.</li><li><strong>{$page.title|lower}:</strong> Esta variable toma el título de la página y lo convierte completamente en minúsculas.</li><li><strong>{$page.title|upper}:</strong> Esta variable toma el título de la página y lo convierte completamente en mayúsculas.</li><li><strong>{$page.title|truncate:5}:</strong> Esta variable toma el título de la página y lo recorta a una longitud máxima de 5 caracteres.</li><li><strong>{Action: nombre_accion}:</strong> Esta acción ejecuta una acción específica definida en la plantilla o en el código de AntCMS.</li><li><strong>{Block: nombre_bloque}:</strong> Esta acción incluye un bloque específico de contenido en la plantilla. El bloque debe estar definido en algún lugar de la plantilla o en un archivo relacionado.</li><li><strong>{Partial: parte_archivo_view}:</strong> Esta acción incluye una parte específica de un archivo de vista en la plantilla.</li><li><strong>{Assets: url_carpeta_publica}:</strong> Esta acción incluye un archivo o recurso ubicado en una carpeta pública de tu aplicación.</li></ul><p>Este glosario proporciona una referencia rápida para utilizar las variables en la plantilla del gestor de contenidos.</p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Creación de Nuevas Variables de Plantilla</h3><p>El gestor de contenidos permite la creación de nuevas variables de plantilla para personalizar aún más la presentación y funcionalidades de las páginas. Para crear una nueva variable de plantilla, sigue estos pasos:</p><ol><li><p><strong>Definir la nueva variable:</strong> Decide el nombre y la funcionalidad de la nueva variable que deseas crear.</p></li><li><p><strong>Agregar la variable al arreglo <code>$templating</code>:</strong> Abre el archivo que contiene el arreglo <code>$templating</code> y agrega una nueva entrada con el siguiente formato:</p><pre class="bg-dark text-light p-2 my-3"><code class="language-php">$templating = [<br/>&#39;{Nueva_variable}&#39; =&gt; &#39;&lt;?php // Código PHP que define el comportamiento de la nueva variable ?&gt;&#39;,<br/>// Otras variables existentes...<br/>];</code></pre><p>Reemplaza <code>&#39;Nueva_variable&#39;</code> con el nombre de tu nueva variable y proporciona el código PHP necesario para que la variable funcione según tus requisitos.</p></li><li><p><strong>Utilizar la nueva variable en la plantilla:</strong> Una vez que hayas definido la nueva variable en el arreglo <code>$templating</code>, puedes utilizarla en tu plantilla HTML de la siguiente manera:</p><pre class="bg-dark text-light p-2 my-3"><code class="language-html">&lt;p&gt;La nueva variable es: {Nueva_variable}&lt;/p&gt;</code></pre><p>Asegúrate de reemplazar <code>&#39;Nueva_variable&#39;</code> con el nombre de la variable que definiste en el paso anterior.</p></li></ol><p>Siguiendo estos pasos, podrás crear y utilizar nuevas variables de plantilla según tus necesidades específicas, lo que te permitirá personalizar aún más la apariencia y funcionalidad de tu sitio web.</p><p><i>Puedes ver un ejemplo en la variable <code>$templating</code> en el archivo <code>index.php</code>.</i></p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Plantillas en AntCMS</h3><p>La estructura de las plantillas en AntCMS se organiza en la carpeta &quot;views&quot;, que contiene varios archivos HTML que representan las diferentes páginas de tu sitio web. Aquí hay una descripción de la estructura y el contenido básico de la carpeta &quot;views&quot;:</p><pre class="bg-dark text-light p-2 my-3"><code>views<br/>├─ partials<br/>│  ├─ footer.inc.html<br/>│  ├─ head.inc.html<br/>├─ 404.html<br/>├─ group.html<br/>└─ index.html</code></pre><p>En la carpeta &quot;views&quot;, tenemos subcarpetas como &quot;partials&quot;, que contiene fragmentos de HTML reutilizables que se incluyen en múltiples plantillas.</p><p>La plantilla básica se encuentra en &quot;index.html&quot; y sigue esta estructura:</p><pre class="bg-dark text-light p-2 my-3"><code class="language-html">{* head (metatags, css, etc..)  *}<br/>{Partial: inc/head.inc.html}<br/>{* action (acción que se ejecuta antes del contenido) *}<br/>{Action: theme_before}<br/>{* contenido *}<br/>{$page.content}<br/>{* action (acción que se ejecuta después del contenido) *}<br/>{Action: theme_after}<br/>{* footer (scripts, etc..) *}<br/>{Partial: inc/footer.inc.html}</code></pre><p>En esta plantilla básica:</p><ul><li>Se incluye el fragmento &quot;head.inc.html&quot; que contiene metatags, enlaces a CSS y cualquier otro contenido relacionado con el encabezado de la página.</li><li>Se ejecuta una acción antes de mostrar el contenido principal de la página, lo que permite realizar operaciones o cargar datos adicionales.</li><li>Se muestra el contenido principal de la página, representado por la variable <code>{$page.content}</code>.</li><li>Se ejecuta una acción después de mostrar el contenido principal, lo que permite realizar operaciones adicionales o cargar elementos adicionales.</li><li>Se incluye el fragmento &quot;footer.inc.html&quot; que contiene scripts u otros elementos relacionados con el pie de página.</li></ul><p>Aquí está la documentación sobre la creación de acciones en AntCMS:</p><h3 class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Acciones</h3><p>Las acciones son funciones que podemos integrar en la plantilla para hacerla más dinámica. Algunas acciones por defecto son:</p><ul><li><strong>head</strong>: Usada para incluir los estilos.</li><li><strong>theme_before</strong>: Comentarios tipo discusión.</li><li><strong>theme_after</strong>: Resolución de formularios.</li><li><strong>footer</strong>: Analytics y JavaScript.</li></ul><h4  class="text-primary mb-3 pb-2 border-bottom border-2 border-light">Creando Acciones</h4><p>Vamos a crear una acción que automáticamente genere un enlace al final de cada página usando una acción que ya está en la plantilla, que es <code>Ant\FrontEnd\FrontEnd::runAction(&#39;theme_after&#39;);</code>.</p><pre class="bg-dark text-light p-2 my-3"><code class="language-php">&lt;?php<br/>Ant\FrontEnd\FrontEnd::actionAdd(&#39;theme_after&#39;, function(){<br/>&nbsp;&nbsp;echo &#39;&lt;a href=&quot;&#39;.Ant\FrontEnd\FrontEnd::urlBase().&#39;/articulos&quot;&gt;Ver artículos.&lt;/a&gt;&#39;;<br/>});<br/>?&gt;</code></pre><p>Y ahora, en todas las páginas al final se verá ese enlace, así de fácil.</p><p>Ahora vamos a añadir algo más. Le diremos que si está en la sección de artículos y la página es &quot;extensiones&quot;, enseñe el texto, y si no, no enseñe nada.</p><pre class="bg-dark text-light p-2 my-3"><code class="language-php">&lt;?php<br/>Ant\FrontEnd\FrontEnd::actionAdd(&#39;theme_after&#39;, function(){<br/>&nbsp;if(Ant\FrontEnd\FrontEnd::urlSegment(0) == &#39;articulos&#39; &amp;&amp; Ant\FrontEnd\FrontEnd::urlSegment(1) == &#39;extensiones&#39;){<br/>&nbsp;&nbsp;echo &#39;&lt;a href=&quot;&#39;.Ant\FrontEnd\FrontEnd::urlBase().&#39;/articulos&quot;&gt;Ver artículos.&lt;/a&gt;&#39;;<br/>&nbsp;}<br/>});<br/>?&gt;</code></pre><p>Ahora haremos una acción que cambie el fondo solo en esta página, para ello usaremos el <code>Ant\FrontEnd\FrontEnd::actionRun(&#39;head&#39;)</code> que hay en el archivo &quot;head.inc.html&quot;.</p><pre class="bg-dark text-light p-2 my-3"><code class="language-php">&lt;?php<br/>Ant\FrontEnd\FrontEnd::actionAdd(&#39;head&#39;, function(){<br/>&nbsp;if(Ant\FrontEnd\FrontEnd::urlSegment(0) == &#39;articulos&#39; &amp;&amp; Ant\FrontEnd\FrontEnd::urlSegment(1) == &#39;extensiones&#39;){<br/>&nbsp;&nbsp;echo &#39;&lt;style rel=&quot;stylesheet&quot;&gt;<br/>&nbsp;&nbsp;&nbsp;body{<br/>&nbsp;&nbsp;&nbsp;&nbsp;background:blue;<br/>&nbsp;&nbsp;&nbsp;&nbsp;color:white;<br/>&nbsp;&nbsp;&nbsp;}<br/>&nbsp;&nbsp;&nbsp;pre, code{<br/>&nbsp;&nbsp;&nbsp;&nbsp;background: #0000bb;<br/>&nbsp;&nbsp;&nbsp;&nbsp;border-color: #00008e;<br/>&nbsp;&nbsp;&nbsp;&nbsp;box-shadow: 0px 3px 6px -2px #02026f;<br/>&nbsp;&nbsp;&nbsp;&nbsp;color: white;<br/>&nbsp;&nbsp;&nbsp;}&lt;/style&gt;&#39;;<br/>&nbsp;}<br/>});<br/>?&gt;</code></pre><p>Con estas acciones, podrás personalizar fácilmente el comportamiento y la apariencia de tus páginas en AntCMS.</p>
        HTML;

        // Generamos el layout
        $html .= '<div class="row gx-2"><div class="col-md-10 col-xl-10 m-auto">' . $content . '</div></div>';
        return $this->viewLayout($html, '', '', '', true);
    }
}
