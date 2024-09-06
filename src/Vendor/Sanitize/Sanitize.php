<?php

declare (strict_types = 1);

namespace Vendor\Sanitize;

final class Sanitize
{
    /**
     * Sanitiza el contenido
     *
     * @param string $str
     * @return string
     */
    public static function content(string $str = ""): string
    {
        $allowed_tags = ALLOWEDTAGS;

        // Sanitiza el contenido con htmlspecialchars
        $sanitizedContents = strip_tags($str, $allowed_tags);
        // Devuelve el contenido sanitizado
        return $sanitizedContents;
    }

    /**
     * Sanitiza el contenido de un archivo
     *
     * @param string $filePath
     * @return string
     */
    public static function file(string $filePath = ""): string
    {
        // Obtiene el contenido del archivo
        $fileContents = file_get_contents($filePath);

        // Sanitiza el contenido con htmlspecialchars
        $sanitizedContents = self::content($fileContents);

        // Devuelve el contenido sanitizado
        return $sanitizedContents;
    }
}
