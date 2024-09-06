<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait Sanitizer - Sanitiza el contenido
 * -----------------------------
 * sanitizeContent: Sanitiza el contenido
 * sanitizeFileContents: Sanitiza el contenido de un archivo
 * ------------------------------
 */
trait SanitizerTrait
{
    /**
     * Sanitiza el contenido
     *
     * @param string $str
     * @return string
     */
    public function sanitizeContent(string $str = ""): string
    {
        // Sanitiza el contenido con htmlspecialchars
        $sanitizedContents = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Devuelve el contenido sanitizado
        return $sanitizedContents;
    }

    /**
     * Sanitiza el contenido de un archivo
     *
     * @param string $filePath
     * @return string
     */
    public function sanitizeFileContents(string $filePath = ""): string
    {
        // Obtiene el contenido del archivo
        $fileContents = file_get_contents($filePath);

        // Sanitiza el contenido con htmlspecialchars
        $sanitizedContents = htmlspecialchars($fileContents, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Devuelve el contenido sanitizado
        return $sanitizedContents;
    }
}