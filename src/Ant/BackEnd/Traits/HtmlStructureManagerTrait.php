<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait HtmlStructureManager - Estructura HTML
 * -----------------------------
 * viewHead: funcion para generar el head
 * viewHeader: funcion para generar el header
 * viewFooter: funcion para generar el footer
 * viewScripts: funcion para generar los scripts
 * viewBreadcrumb: funcion para generar el breadcrumb
 * displayErrorLayout: plantilla de error
 * ------------------------------
 */
trait HtmlStructureManagerTrait
{
    /**
     * Generamos el head
     *
     * @param string $otherCss css
     * @return string
     */
    public function viewHead(string $otherCss = ""): string
    {
        // Framework Bootstrap
        $links = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">';

        // Retornar la sección head del HTML
        return '<head><meta charset="utf-8"><title>' . SITE_TITLE . '</title><link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>' . EMOJI_FAVICON . '</text></svg>"><meta name="viewport" content="width=device-width, initial-scale=1.0" /><meta name="application-name" content="' . SITE_TITLE . '" /><meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"><meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Expires" content="0"><meta name="referrer" content="no-referrer-when-downgrade"><meta name="robots" content="noindex,nofollow">' . $links . '<style rel="stylesheet">:PUBLIC_ROOT{--bg-pattern: repeating-conic-gradient( var(--bs-body-bg) 0% 25%, var(--lt-color-gray-200) 0% 50% ) 50% / 20px 20px;}img{max-width:100%;}</style><style rel="stylesheet">' . $otherCss . '</style></head>';
    }

    /**
     * Generamos el header
     *
     * @param string $url url de la web
     * @param string $title titulo
     * @param string $logo logo
     * @param string $homeIcon icono de home
     * @param string $logoutTpl plantilla de e enlace de logout
     * @return string
     */
    public function viewHeader(string $url, string $title, string $logo, string $logoutTpl): string
    {
        $navlinks = '<a class="nav-link" href="' . $url . '">' . $this->lang('home') . '</a>';
        $navlinks .= '<a class="nav-link" rel="noopener" target="_blank" href="' . SITE_URL . '">' . $this->lang('viewWeb') . '</a>';
        $navlinks .= '<a class="nav-link" href="' . $url . '?get=utilities&name">' . $this->lang('utilities') . '</a>';
        $navlinks .= '<a class="nav-link" href="' . $url . '?get=help&name">' . $this->lang('help') . '</a>';

        return '<header class="header"><nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid"><a class="navbar-brand" href="' . $url . '"><img class="rounded-pill me-2" src="' . $logo . '" alt="logo"><span>' . $title . '</span></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation" title="Toggle navigation"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNavAltMarkup"><div class="navbar-nav ms-auto mb-2 mb-lg-0">' . $navlinks . $logoutTpl . '</div></div></div></nav><div class="progress rounded-0" style="display:none; height:3px;"><div id="progress-bar" title="Barra de progreso" class="progress-bar bg-danger text-light" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height:3px;"></div></header>';
    }

    /**
     * Generamos el footer
     *
     * @return string
     */
    public function viewFooter(): string
    {
        $year         = date('Y');
        $ip           = $this->getDesktopIp();
        $renderIpTmpl = $ip ? '- <a href="http://' . $ip . '" target="_self" rel="noopener">' . $ip . '</a>' : '';
        return '<footer class="footer mt-4 text-center"><div class="container-fluid"><div class="row"><div class="col-md-12"><p class="copyright"><small>Made with ♥ Moncho Varela © ' . $year . $renderIpTmpl . ' </small></p></div></div></div></footer>';
    }

    /**
     * Generamos el html del javascript
     *
     * @param string $js
     * @return string
     */
    public function viewScripts(string $js): string
    {
        // imprimimos la session
        $session = $this->msgGet('msg');
        $scripts = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>';
        $scripts .= '<script rel="javascript" nonce="' . NONCE . '">function message(title,msg){const html=`<div class="toast show fixed-top m-2" role="alert" aria-live="assertive" aria-atomic="true" id="msg-notification"><div class="toast-header"><span class="bg-primary p-1 rounded-pill mx-2" style="width:5px;height:5px;"></span><strong class="me-auto">${title}</strong></div><div class="toast-body">${msg}</div></div>`;document.body.innerHTML+=html;let w=setTimeout(()=>{document.getElementById("msg-notification").remove();clearTimeout(w);},2000);}</script>';
        $scripts .= '<script rel="javascript" nonce="' . NONCE . '">' . $js . '</script>';
        $scripts .= $session;
        return $scripts;
    }

    /**
     * Genera el breadcrumb en formato HTML
     *
     * @param string $path La ruta del directorio actual
     * @param string $PUBLIC_ROOT La ruta de la carpeta raíz
     * @return string El breadcrumb en formato HTML
     */
    public function viewBreadcrumb(string $path = "", string $PUBLIC_ROOT = ""): string
    {
        // Separamos las carpetas de la ruta
        $folders = explode('/', str_replace(PUBLIC_ROOT, '', $path));
        $url     = ADMIN_URL . ADMIN_FILE_NAME;

        // Iniciamos el breadcrumb con el enlace a la carpeta raíz
        $breadcrumb = '<nav aria-label="breacrumb"><ol class="breadcrumb"><li class="breadcrumb-item fw-bold" aria-current="page"><a class="text-decoration-none text-black" href="' . $url . '">Inicio</a></li>';

        // Creamos los enlaces a cada carpeta
        $route = '';
        foreach ($folders as $folder) {
            if (!empty($folder)) {
                $route .= '/' . $folder;
                // Comprobamos si es un directorio o un archivo
                $breadcrumb .= (is_dir(PUBLIC_ROOT . $route)) ? '<li class="breadcrumb-item"><a class="text-dark text-decoration-none" href="' . $url . '?get=dir&name=' . base64_encode($route) . '">' . $folder . '</a></li>' : '';
            }
        }
        // Cerramos el breadcrumb
        $breadcrumb .= '</ol></nav>';
        return $breadcrumb;
    }

    /**
     * Layout html
     *
     * @param string $content // Contenido que se va a incluir en el layout
     * @param string $css // Ruta al archivo CSS que se va a incluir
     * @param string $js // Ruta al archivo JavaScript que se va a incluir
     * @return string // El HTML del layout
     */
    public function viewLayout(string $content = "", string $css = "", string $js = "", string $current = "", bool $showHeader = true): string
    {

        $url   = ADMIN_URL . ADMIN_FILE_NAME; // URL del sitio web
        $title = SITE_TITLE; // Título del sitio web
        $logo  = LOGO; // Logo del sitio web

        // Carpeta donde se subirán archivos, si está definida
        $folderToUpload = ($current) ? base64_encode($current) : '';

        // Llamamos a la función createBreadcrumb
        $breadcrumb = ($showHeader) ? $this->viewBreadcrumb($current, PUBLIC_ROOT) : '';

        // boton de logout que solo sale si estamos logueados
        $logoutTpl = ($this->isLoggedIn()) ? '<a class="nav-link" href="' . $url . '?logout=true">' . $this->lang('logout') . '</a>' : '';

        // Llamamos a la función viewHead
        $head = $this->viewHead($css);

        // Llamamos a la función viewHeader
        $header = ($showHeader) ? $this->viewHeader($url, $title, $logo, $logoutTpl) : '';

        // Llamamos a la función viewFooter
        $footer = $this->viewFooter();

        // Llamamos a la función viewScripts
        $scripts = $this->viewScripts($js);

        // plantilla html
        return '<!Doctype html><html lang="' . HTML_LANG . '">' . $head . '<body id="top" data-theme="light"><main id="app">' . $header . '<section class="container-fluid py-3 pb-1"><div class="row"><div class="col-md-12">' . $breadcrumb . '</div></div></section><section class="container-fluid">' . $content . '</section>' . $footer . '</main>' . $scripts . '</body></html>';
    }

    /**
     * Error layout
     *
     * @param string $content
     * @return void
     */
    public function displayErrorLayout(string $content = '')
    {
        // plantilla html
        return '<!Doctype html><html lang="' . HTML_LANG . '"><head><meta charset="utf-8"><title>' . SITE_TITLE . '</title><link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>' . EMOJI_FAVICON . '</text></svg>"><meta name="viewport" content="width=device-width, initial-scale=1.0" /><meta name="application-name" content="' . SITE_TITLE . '" /><meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"><meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Expires" content="0"><meta name="referrer" content="no-referrer-when-downgrade"><meta name="robots" content="noindex,nofollow"></head><body><div style="height: 100vh; display: flex; align-items: center; justify-content: center"><p>' . $this->lang('error') . ' ' . $this->lang('errorAccess') . '</p></did></body></html>';
    }
}
