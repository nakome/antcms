<?php

declare (strict_types = 1);

namespace Traits;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

trait Contact
{
    /**
     * Random captcha
     *
     * @param string $input
     * @param int ·$strlen
     *
     * @return string
     */
    public static function captcha(
        string $input = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        int $strlen = 6
    ): string {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strlen; ++$i) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    /**
     * Contacto
     *
     * @return void
     */
    public static function contact(): void
    {
        // mensajes para traducir
        $messages = [
            'name' => 'Nombre',
            'email' => 'Correo electronico',
            'phone' => 'Telefono',
            'subject' => 'Asunto',
            'message' => 'Mensaje',
            'captchaLabel' => 'Por favor escriba <strong>{}</strong> para validar el mensaje.',
            'errorCaptchaInfo' => '<strong>Error: </strong> el codigo de validación introducido es incorrecto.',
            'submitBtn' => 'Enviar correo',
            'pagetitle' => 'Nuevo mensaje desde la web',
            'successInfo' => 'Gracias tu mensaje ha sido enviado, volviendo al inicio en 3 segundos.',
            'errorInfo' => '<strong>Error: </strong>Lo siento hubo un problema al enviarlo por favor intentelo otra vez..',
        ];

        // numero aleatorio
        $captchaValue = self::captcha('123456789', 6);
        // email
        $recepient = AntCMS::$config['email'];
        // titulo email
        $sitename = AntCMS::$config['title'];
        // url sitio
        $siteurl = AntCMS::urlBase();

        // info click submit
        $infoOutput = '';

        // si se envia el formulario
        if (array_key_exists('enviarFormulario',$_POST)) {

            // post vars
            $subject = trim($_POST['subject']);
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = (trim($_POST['phone'])) ? trim($_POST['phone']) : 'no proporcionado';
            $text = trim($_POST['message']);
            $captcha = trim($_POST['captcha']);
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
                // enviamos mail
                if (mail($recepient, $pagetitle, $message, "Content-type: text/plain; charset=\"utf-8\" \nFrom: <$email>")) {
                    // pasamos la info de que ha sido mandando
                    $infoOutput = '<div class="alert alert-info">' . $messages['successInfo'] . '</div>';
                    // volvemos al inicio
                    echo "<script rel='javascript'>setTimeout(() => {window.location.href = site_url;},3000);</script>";
                } else {
                    // error email no enviado
                    $infoOutput = '<div class="alert alert-danger">' . $messages['errorInfo'] . '</div>';
                }
            } else {
                // info error captcha
                $infoOutput = '<div class="alert alert-danger">' . $messages['errorCaptchaInfo'] . '</div>';
            }
        }

        // show error
        $html = $infoOutput;

        // iniciamos formulario html
        $html .= '<form id="contact" class="form mb-3 needs-validation" method="post">';
        // input hidden
        $html .= static::inputHiddenForm([
            'name' => 'checkCaptcha',
            'value' => $captchaValue,
        ]);
        // input name
        $html .= static::inputForm([
            'name' => 'name',
            'type' => 'text',
            'label' => $messages['name'],
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input email
        $html .= static::inputForm([
            'name' => 'email',
            'type' => 'email',
            'label' => $messages['email'],
            'pattern' => '[a-z._%+-]+@[a-z.-]+\.[a-z]{2,4}',
            'required' => (bool)true,
        ]);
        // input phone
        $html .= static::inputForm([
            'name' => 'phone',
            'type' => 'tel',
            'label' => $messages['phone'],
            'pattern' => '[0-9]{9,9}',
            'required' => (bool)true,
        ]);
        // input subject
        $html .= static::inputForm([
            'name' => 'subject',
            'type' => 'text',
            'label' => $messages['subject'],
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input message
        $html .= static::textareaForm([
            'name' => 'message',
            'label' => $messages['message'],
            'rows' => '5',
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input captcha
        $html .= static::inputForm([
            'name' => 'captcha',
            'type' => 'number',
            'label' => str_replace('{}', $captchaValue, $messages['captchaLabel']),
            'required' => (bool)true,
        ]);
        // input submit
        $html .= static::submitForm([
            'name' => 'enviarFormulario',
            'value' => $messages['submitBtn'],
        ]);
        // cerramos formulario
        $html .= '</form>';
        echo $html;
    }

    /**
     * input form
     */
    public static function inputForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $label = $args['label'] ?? 'input text';
        $placeholder = $args['placeholder'] ?? '';
        $type = $args['type'] ?? 'text';
        $pattern = $args['pattern'] ?? 'name';
        $required = $args['required'] ? 'required' : '';
        $value = $args['value'] ?? '';
        return <<<HTML
            <div class="form-group">
                <label for="{$name}" class="form-label">{$label}</label>
                <input
                    class="form-control"
                    type="{$type}"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    placeholder="{$placeholder}"
                    value="{$value}"
                    {$required}
                />
            </div>
        HTML;
    }

    /**
     * input hidden form
     */
    public static function inputHiddenForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    }

    /**
     * submit form
     */
    public static function submitForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        $class = $args['class'] ?? 'btn';
        return '<input class="' . $class . '" type="submit" name="' . $name . '" value="' . $value . '" />';
    }

    /**
     * textarea form
     */
    public static function textareaForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $label = $args['label'] ?? 'message';
        $placeholder = $args['placeholder'] ?? '';
        $type = $args['type'] ?? 'text';
        $pattern = $args['pattern'] ?? '';
        $rows = $args['rows'] ?? '5';
        $required = $args['required'] ? 'required' : '';
        $value = $args['value'] ?? '';
        return <<<HTML
            <div class="form-group">
                <label for="{$name}" class="form-label">{$label}</label>
                <textarea
                    class="form-control"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    rows="{$rows}"
                    placeholder="{$placeholder}"
                    {$required}
                >{$value}</textarea>
            </div>
        HTML;
    }
}
