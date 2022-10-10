<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Text
{
    /**
     *  Acortar texto.
     *
     *  @param string $text
     *  @param int $int
     *
     *  @return string
     */
    public static function short(
        string $text, 
        int $chars_limit
    ):string
    {
        // Compruebe si la longitud es mayor que el límite de caracteres
        if (strlen($text) > $chars_limit) {
            $text = strip_tags($text);
            // resolver ñ bug
            //$text = htmlentities(html_entity_decode($text));
            // Si es así, acorta en el límite de caracteres
            $new_text = substr($text, 0, $chars_limit);
            // Recortar los espacios en blanco
            $new_text = trim($new_text);
            // Añadir al final ...
            return $new_text . '...';
        } else {
            return strip_tags($text);
        }
    }
}
