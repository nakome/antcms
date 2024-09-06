<?php

declare (strict_types = 1);

defined('ACCESS') or die('Acceso denegado');

use Vendor\Action\Action as Action;
use Vendor\Form\Form as Form;
use Vendor\Url\Url as Url;

/** ==============================================================
 *  Funciones para la plantilla de AntCMS
 *  Author: Moncho Varela
 *  Email: nakome@gmail.com
 * ===============================================================*/

// Iniciamos las funcion para la busqueda
Action::add("theme_before", fn() => query());

/**
 * Funcion para mostrar el formulario de contacto
 */
if (!function_exists('contact')) {
    function contact()
    {
        // numero aleatorio
        $captchaValue = captcha('123456789', 6);
        // email
        $recepient = CONTACT_EMAIL;
        // titulo email
        $sitename = SITE_TITLE;
        // url sitio
        $siteurl = SITE_URL;

        // mensajes para traducir
        $messages = [
            'name'             => 'Nombre',
            'email'            => 'Correo electronico',
            'phone'            => 'Telefono',
            'subject'          => 'Asunto',
            'message'          => 'Mensaje',
            'captchaLabel'     => 'Por favor escriba <strong>{}</strong> para validar el mensaje.',
            'errorCaptchaInfo' => 'Error:  el codigo de validación introducido es incorrecto.',
            'submitBtn'        => 'Enviar correo',
            'closeAlert'       => 'Cerrar ventana',
            'pagetitle'        => 'Nuevo mensaje desde la web',
            'successTitle'     => 'Bien 😀',
            'successInfo'      => 'Gracias tu mensaje ha sido enviado, en breve le contestaremos.',
            'errorTitle'       => 'Ups, 🤔',
            'errorInfo'        => 'Error: Lo siento hubo un problema al enviarlo por favor intentelo otra vez..',
            'policy'           => '<p style="font-size:12px;">De conformidad con lo dispuesto en el Reglamento General de Protección de Datos de la UE (RGPD) y en la <strong>Ley Orgánica de Protección de Datos 3/2018 de 5 de diciembre</strong>, le informamos que los datos que nos facilite forman parte de un tratamiento del que es responsable <strong>DATOS DE PERSONA</strong> con la finalidad de gestionar y responder las solicitudes de información y/o contacto recibidas. La legitimación del tratamiento se basa en el consentimiento del interesado. No se cederán datos a terceros salvo obligación legal. Podrá ejercitar los derechos de acceso, rectificación y supresión de los datos, así como otros derechos, tal y como se explica en la información adicional. Puede consultar la información adicional y detallada sobre protección de datos clicando en la POLÍTICA DE PRIVACIDAD o enviando un email a: <strong>' . CONTACT_EMAIL . '</strong></p>',
        ];

        // info click submit
        $infoOutput = '';

        // si se envia el formulario
        if (array_key_exists('enviarFormulario', $_POST)) {

            // post vars
            $subject      = trim($_POST['subject']);
            $name         = trim($_POST['name']);
            $email        = trim($_POST['email']);
            $phone        = (trim($_POST['phone'])) ? trim($_POST['phone']) : 'no proporcionado';
            $text         = trim($_POST['message']);
            $captcha      = trim($_POST['captcha']);
            $checkCaptcha = trim($_POST['checkCaptcha']);

            // titulo del mensaje
            $pagetitle = $messages['pagetitle'];

            // mensaje de salida
            $message = "========= INFO ============\n";
            $message .= $messages['name'] . ": $name\n";
            $message .= $messages['email'] . ": $email\n";
            $message .= $messages['phone'] . ": $phone\n";
            $message .= $messages['subject'] . ": $subject\n";
            $message .= "===========================\n";
            $message .= $text;

            // comprobamos el captcha
            if ($checkCaptcha == $captcha) {
                // captcha ok, enviamos email
                if (mail($recepient, $pagetitle, $message, "Content-type: text/plain; charset=\"utf-8\" \nFrom: <$email>")) {
                    // pasamos la info de que ha sido mandando
                    $infoOutput = $messages['successInfo'];
                    $infoTitle  = $messages['successTitle'];
                    $infoBtn    = $messages['closeAlert'];
                    // vemos alerta y refrescamos
                    echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
                    <script rel='javascript' nonce='" . NONCE . "'>
                        swal({
                            title: '{$infoTitle}',
                            text: '{$infoOutput}',
                            button: '{$infoBtn}'
                        }).then(() => {location.href = site_url});
                    </script>";
                } else {
                    // error email no enviado
                    $infoOutput = $messages['errorInfo'];
                    $infoTitle  = $messages['errorTitle'];
                    $infoBtn    = $messages['closeAlert'];
                    // vemos alerta y refrescamos
                    echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
                    <script rel='javascript' nonce='" . NONCE . "'>
                        swal({
                            title: '{$infoTitle}',
                            text: '{$infoOutput}',
                            button: '{$infoBtn}'
                        }).then(() => {location.href = site_url+'#contact'});
                    </script>";
                }
            } else {
                // info error captcha
                $infoOutput = $messages['errorCaptchaInfo'];
                $infoTitle  = $messages['errorTitle'];
                $infoBtn    = $messages['closeAlert'];
                // vemos alerta y refrescamos
                echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
                <script rel='javascript' nonce='" . NONCE . "'>
                    swal({
                        title: '{$infoTitle}',
                        text: '{$infoOutput}',
                        button: '{$infoBtn}'
                    }).then(() => {location.href = site_url+'#contact'});
                </script>";
            }
        }

        // iniciamos formulario html
        $html = '<form id="contact" class="form mb-3 needs-validation" method="post">';
        // input hidden
        $html .= Form::hidden([
            'name'  => 'checkCaptcha',
            'value' => $captchaValue,
        ]);

        // dividimos en 2 columnas
        $html .= '<div class="row"><div class="col-md-6">';

        // input name
        $html .= Form::input([
            'name'        => 'name',
            'type'        => 'text',
            'label'       => 'Nombre',
            'placeholder' => 'Escriba su nombre',
            'pattern'     => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required'    => (bool) true,
        ]);

        // input email
        $html .= Form::input([
            'name'        => 'email',
            'type'        => 'email',
            'label'       => 'Email',
            'placeholder' => 'Escriba su correo electrónico',
            'pattern'     => '[a-z._%+-]+@[a-z.-]+\.[a-z]{2,4}',
            'required'    => (bool) true,
        ]);

        // dividimos en 2 columnas
        $html .= '</div><div class="col-md-6">';

        // input phone
        $html .= Form::input([
            'name'        => 'phone',
            'type'        => 'tel',
            'label'       => 'Telefono',
            'placeholder' => 'Escriba su teléfono',
            'pattern'     => '[0-9]{9,9}',
            'required'    => (bool) true,
        ]);

        // input subject
        $html .= Form::input([
            'name'        => 'subject',
            'type'        => 'text',
            'label'       => 'Asunto',
            'placeholder' => 'Escriba su asunto aquí',
            'pattern'     => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required'    => (bool) true,
        ]);

        // dividimos en 1 columna
        $html .= '</div><div class="col-md-12">';

        // input message
        $html .= Form::textarea([
            'name'        => 'message',
            'label'       => 'Mensaje',
            'placeholder' => 'Escriba su mensaje aquí',
            'rows'        => '5',
            'pattern'     => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required'    => (bool) true,
        ]);

        // dividimos en 1 columna
        $html .= '</div><div class="col-md-12">';

        // input captcha
        $html .= Form::input([
            'name'        => 'captcha',
            'type'        => 'number',
            'label'       => str_replace('{}', $captchaValue, $messages['captchaLabel']),
            'placeholder' => 'Escriba el numero que aparece arriba',
            'required'    => (bool) true,
        ]);

        $html .= '</div><div class="col-md-12 mt-3">';
        $html .= $messages['policy'];
        $html .= '</div><div class="col-md-12">';

        // checkbox
        $html .= Form::checkbox([
            'name'     => 'terms',
            'type'     => 'checkbox',
            'label'    => 'He leído y acepto la <a href="' . $siteurl . '/politica-de-privacidad">Política de privacidad</a>',
            'required' => (bool) true,
            'class'    => 'form-checkbox',
        ]);

        // input submit
        $html .= Form::submit([
            'name'  => 'enviarFormulario',
            'value' => $messages['submitBtn'],
            'class' => 'btn btn-outline-dark mx-0 my-3',
        ]);

        // cerramos columna y row
        $html .= '</div></div>';
        // cerramos formulario
        $html .= '</form>';
        return $html;
    }
}

/**
 * Funcion para la busqueda
 */
if (!function_exists('query')) {
    function query()
    {

        // Verifica si se ha enviado una consulta de búsqueda
        if (!isset($_GET['buscar'])) {
            return;
        }

        // Limpia y almacena la consulta de búsqueda
        $query = trim($_GET['buscar']);
        if (empty($query)) {
            return;
        }

        // Obtiene todas las páginas del sitio, ordenadas por fecha descendente, y filtra las que tienen el estado "404"
        $pages = Ant\FrontEnd\FrontEnd::run()->pages('/', 'date', 'ASC', ['404'], 0);

        // Filtra las páginas que contienen la consulta de búsqueda en cualquiera de sus campos relevantes
        $results = array_filter($pages, function ($page) use ($query) {
            return preg_match("/$query/i", $page['title']) ||
            preg_match("/$query/i", $page['description']) ||
            preg_match("/$query/i", $page['tags']) ||
            preg_match("/$query/i", $page['author']) ||
            preg_match("/$query/i", $page['keywords']) ||
            preg_match("/$query/i", $page['slug']) ||
            preg_match("/$query/i", date('d-m-Y', (int) $page['date']));
        });

        // Obtiene el número total de resultados y genera el HTML de salida
        $total        = count($results);
        $searchOutput = renderQuery($results);
        $html         = "<article class='bg-primary text-light p-4'>{$searchOutput}<footer><strong class='text-secondary'>{$total}</strong> Resultado/s de {$query}</footer></article>";

        // Si no se encontraron resultados, muestra un mensaje apropiado
        if (!$results) {
            $html = "<article class='bg-primary text-light p-4'><h3 class='fs-4'>No hay resultados de {$query}</h3></article>";
        }

        // Imprime el HTML generado
        echo $html;
    }
}

/**
 * Renderiza el resultado de la búsqueda
 */
if (!function_exists('renderQuery')) {
    function renderQuery(array $results): string
    {
        // Se inicializa la variable $html con una lista HTML
        $html = '<ul class="list-unstyled">';

        // Se recorren los resultados y se construye un HTML por cada página
        foreach ($results as $page) {
            // Se construye la URL de la página
            $url = Url::base() . rtrim(str_replace('//', '/', str_replace(Url::base(), '', $page['url'])), '/');
            // Se convierte la fecha de la página en formato dd-mm-aaaa
            $dateFormat = date('d-m-Y', (int) $page['date']);
            // Se agrega el HTML de la página a la lista
            $html .= <<<HTML
                     <li>
                         <strong>{$dateFormat}</strong>
                         <span> - </span>
                         <a class="text-secondary" href="{$url}">{$page['title']}</a>
                     </li>
                 HTML;
        }

        // Se cierra la lista HTML y se devuelve el resultado
        $html .= '</ul>';
        return $html;

    }
}

/**
 * Funcion recursiva para crear el menu
 */
if (!function_exists('createMenu')) {
    function createMenu(array $nav, string $type = "nav-link")
    {
        $html = '';
        // iterar sobre los elementos del menú
        foreach ($nav as $k => $v) {
            // ignorar elementos vacíos o que no sean válidos
            if (empty($k) || !is_string($k) || empty($v)) {
                continue;
            }
            // si el elemento del menú es un enlace externo
            if (preg_match('/http/i', $k)) {
                // agregar un enlace
                $html .= '<li class="nav-item"><a class="' . $type . '" href="' . $k . '">' . ucfirst($v) . '</a></li>';
            } else {
                // obtener la URL base del sitio y la URL actual
                $urlBase    = Url::base() . $k;
                $currentUrl = Url::current();
                // verificar si el elemento actual del menú está activo
                $active = ($currentUrl == str_replace('/', '', $k)) ? 'class="' . $type . ' active"' : 'class="' . $type . '"';
                // si el elemento del menú es un submenú
                if (is_array($v)) {
                    // agregar el submenú
                    $html .= '<li class="nav-item dropdown">';
                    $html .= '<a class="' . $type . '" role="button" aria-expanded="false" data-bs-toggle="dropdown" href="#">' . ucfirst($k) . '</a>';

                    $html .= '<ul class="list-unstyled dropdown-menu dropdown-menu-dark" style="min-width:170px">';
                    $html .= createMenu($v, "dropdown-item");
                    $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    // agregar un elemento de menú
                    $html .= '<li class="nav-item"><a ' . $active . ' href="' . $urlBase . '">' . ucfirst($v) . '</a></li>';
                }
            }
        }
        return $html;
    }
}

/**
 * Genera un valor aleatorio para el captcha
 */
if (!function_exists('captcha')) {
    function captcha(string $input = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890', int $strlen = 6): string
    {
        $result = '';
        for ($i = 0; $i < $strlen; ++$i) {
            $result .= $input[mt_rand(0, strlen($input) - 1)];
        }
        return $result;
    }

}

/**
 * Trunca un texto
 */
if (!function_exists('truncate')) {
    function truncate($text, $length)
    {
        if (strlen($text) > $length) {
            $truncated_text = substr($text, 0, $length) . "...";
            return $truncated_text;
        } else {
            return $text;
        }
    }
}

/**
 * Formatea el tiempo en dias
 */
if (!function_exists('formatTime')) {
    function formatTime($timestamp)
    {

        $now        = time();
        $difference = $now - $timestamp;

        // Segundos en un minuto, hora, día y mes
        $minute = 60;
        $hour   = $minute * 60;
        $day    = $hour * 24;
        $month  = $day * 30;

        $output = '';
        if ($difference < $minute) {
            $output .= 'hace unos segundos';
        } elseif ($difference < $hour) {
            $minutes = floor($difference / $minute);
            $output .= "hace $minutes minutos";
        } elseif ($difference < $day) {
            $hours = floor($difference / $hour);
            $output .= "hace $hours horas";
        } elseif ($difference < 2 * $day) {
            $output .= 'ayer';
        } elseif ($difference < $month) {
            $days = floor($difference / $day);
            $output .= "hace $days días";
        } elseif ($difference < 2 * $month) {
            $output .= "hace 1 mes";
        } else {
            $months = floor($difference / $month);
            $output .= "hace $months meses";
        }
        return $output;
    }
}
