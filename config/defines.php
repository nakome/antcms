<?php

declare (strict_types = 1);

// Definir constantes globales para configuración
define('HTML_LANG', 'es'); // Idioma y región de el Doctype
define('LANG', 'es_ES'); // Idioma y región
define('LANG_FALLBACK_1', 'Spanish_Spain'); // Fallback 1 para el idioma
define('LANG_FALLBACK_2', 'Spanish'); // Fallback 2 para el idioma
define('TIMEZONE', 'Europe/Brussels'); // Zona horaria
define('CHARSET', 'UTF-8'); // Codificación de caracteres
define('ROOT_MINIMUM_PHP', '7.4'); // Define la versión mínima de PHP requerida
define('ACCESS', true); // Control de acceso a los archivos
define('NONCE', base64_encode(random_bytes(16))); // Define el nonce para Content-Security-Policy
define('DEBUG', true); // Establecer si el modo DEBUG está activado
define('ROOT', str_replace(DIRECTORY_SEPARATOR, '/', getcwd())); // Define la ruta del directorio raiz
define('PUBLIC_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', dirname(getcwd()))); // Define la ruta pública (Solo para admin 🤠)
define('POWERED_BY', 'Moncho Varela :)'); // X-Powered-By personalizado
define('CONTENT', ROOT . '/public/content');
define('THEME', ROOT . '/public/views');

// Seguridad y cabeceras
define('STRICT_TRANSPORT_SECURITY', 'max-age=31536000');
define('CONTENT_SECURITY_POLICY', "img-src 'self' data:; script-src 'self' https://cdn.jsdelivr.net https://cpwebassets.codepen.io https://unpkg.com 'nonce-%s'");
define('X_FRAME_OPTIONS', 'SAMEORIGIN');
define('X_CONTENT_TYPE_OPTIONS', 'nosniff');
define('REFERRER_POLICY', 'no-referrer-when-downgrade');
define('PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=()');

// Configuración de sesión y cookies
define('SESSION_USE_ONLY_COOKIES', 1);
define('SESSION_USE_TRANS_SID', 0);
define('SESSION_COOKIE_HTTPONLY', 1);

// Configuración de logging
define('ERROR_LOG_FILE', PUBLIC_ROOT . '/tmp/php-error.log');

// Centralizar URLs
define('SITE_URL', 'http://localhost/github/antcms');
define('ADMIN_URL', SITE_URL . '/');
define('ADMIN_FILE_NAME', 'panel/index.php');

// Seguridad
define('PASSWORD_HASH', '$2y$10$FVUFebwDtNUnjBwAoZVA1e6jXclZaJh24KUw1Wdwn8wVgk4r5DAgS'); // demo@123
define('EMOJI_FAVICON', '🐱‍👤');
define('LOGO', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAjpJREFUWEftlcsrRFEcx3/TlCahSFkgyqORKQszyiOFjTw2SikLOxsrZSNqUiYbZeUPsFBKzUZkg5RHcSzUlMmjCAslCjHJNPqd8buOa849584jG7/Vveeec76f8/3+TtdRGxyOwR+W4x9Ax4HxkZOkQpqZr1Gu04qAAAYu1pUb4oSlik4+Ly0AJD54uQrRqNPYXEaCkE5nFBbLu7UglA6YTz9d1AD++32ArFyYyvNwEf9TCOD9GaYKG2Hy7oCP6bpgG0Arg0wB0KlUEBhD2h1A0fOXB5U2/16ZU2DMUzWiVgQkjNl31eUZm68dP/Fn8xj2AoGkDYA3no0iiOWFZstVSgf6h3ZhpdoFbG8LvE1tPzbDMaxE4zjWexqBlABC3bO/BBhj8TGv9ycMY4B/NZ8wToCe1TGpC5YOEIB4ykPGYLTVATuv9dwV+taSfQRz2zHw+eJg9A2fUwOIPAK48qVWyyLgC77WJg2Ae4gu2OhBY6qVOE5SNmEiCHfp91UUocLX8WtJpRJPCkAmTqIiRMYAuAjmK5YrHxAu4wCWfZCVy/+KaY9A1owUhzl71dUTD6HVhLQg1OPnj+6SYgjf3AK8ffD3WLYTaopLDPt1sqc9bQFwJzomwF1VljCJ8NkVeDYCtm5rUgDcBRMEinPrMwngChwB2wxantDb3geRiXptF7QdQHEsHQCcpwuhBUDidgB0IZQAorjoq9kJtD5RqZywBJCJkxBByMRpnhWEFEAlLsahArCK488BPgGWqTzwXrlG8gAAAABJRU5ErkJgggAA');

// Exclusiones de archivos
define('EXCLUDE_DIRECTORIES', ['tmp', 'config', 'panel', 'src']);

// Configuración del tema
define('GOOGLE_SITE_VERIFICATION', ''); // Verificación de Google
define('THEME_COLOR', '#355C7D'); // Color del tema
define('BACKGROUND_COLOR', '#ffffff'); // Color de fondo
define('ORIENTATION', 'portrait'); // Orientación
define('DISPLAY_MODE', 'fullscreen'); // Modo de visualización
define('SHORT_NAME', 'antcms'); // Nombre corto

// Metadatos del sitio
define('SITE_TITLE', 'AntCMS'); // Título del sitio
define('SITE_DESCRIPTION', 'Flat file CMS.'); // Descripción
define('SITE_KEYWORDS', 'php,flat,file,cms'); // Palabras clave
define('AUTHOR', 'Moncho Varela'); // Autor del sitio
define('CONTACT_EMAIL', 'nakome@gmail.com'); // Correo de contacto

// Imagen por defecto y configuración de paginación
define('DEFAULT_IMAGE', 'public/notfound.jpg'); // Imagen por defecto
define('PAGINATION_LIMIT', 2); // Límite de paginación

// Derechos de autor
define('COPYRIGHT', 'AntCMS');

// Navegación del sitio
define('MENU', [
    '/'              => 'Inicio',
    '/documentacion' => 'Documentación',
    '/blog'          => 'Blog',
    '/abcd'          => 'Error',
]);

// Leer más
define('READ_MORE', 'Puede leer más aquí');

// Página no publicada
define('NOT_PUBLISHED', [
    'title'       => 'Página no publicada',
    'description' => 'La página a la que está accediendo aún no se ha publicado o se ha desactivado',
    'content'     => '<div class="bg-light p-5 shadow"><h1 class="text-danger">Página no publicada</h1><p class="lead">La página a la que está accediendo aún no se ha publicado o se ha desactivado.</p></div>',
]);

// Etiquetas permitidas para usar en el contenido
define('ALLOWEDTAGS','<style><script><div><span><p><a><img><button><ul><ol><li><strong><em><b><i><u><h1><h2><h3><h4><h5><h6><table><thead><tbody><tfoot><tr><th><td><form><input><select><option><textarea><label><fieldset><legend><iframe><blockquote><pre><code><figure><figcaption><hr><br><section><article><main><aside><nav><header><footer><details><summary><svg><path>');


// Dar formato a la fecha y región usando setlocale
setlocale(LC_ALL, LANG, LANG_FALLBACK_1, LANG_FALLBACK_2);

// Comparación de versión de PHP
if (version_compare($ver = PHP_VERSION, $req = ROOT_MINIMUM_PHP, '<')) {
    $out = sprintf('Usted está usando PHP %s, pero AntCMs necesita <strong>PHP %s</strong> para funcionar.', $ver, $req);
    exit($out);
}

// Establecer la codificación interna a UTF-8
mb_internal_encoding(CHARSET);

// Cabeceras de seguridad
header("X-Powered-By: " . POWERED_BY);
header('Strict-Transport-Security: ' . STRICT_TRANSPORT_SECURITY);
header(sprintf('Content-Security-Policy: ' . CONTENT_SECURITY_POLICY, NONCE));
header('X-Frame-Options: ' . X_FRAME_OPTIONS);
header('X-Content-Type-Options: ' . X_CONTENT_TYPE_OPTIONS);
header('Referrer-Policy: ' . REFERRER_POLICY);
header('Permissions-Policy: ' . PERMISSIONS_POLICY);

// Establecer el charset
header('Content-Type: text/html; charset=' . CHARSET);

// Configuración recomendada para caracteres en español
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding(CHARSET);
function_exists('mb_internal_encoding') and mb_internal_encoding(CHARSET);

// Zona horaria
@ini_set('date.timezone', TIMEZONE);
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(TIMEZONE);
} else {
    putenv('TZ=' . TIMEZONE);
}

// Si DEBUG es true, mostramos los errores
if (DEBUG == true) {
    @ini_set('error_reporting', (string) E_ALL);
    @ini_set('display_errors', (string) 1);
} else {
    @ini_set('error_reporting', (string) E_ALL);
    @ini_set('display_errors', (string) 0);
}

// Configuración de sesión y seguridad
ini_set('session.use_only_cookies', SESSION_USE_ONLY_COOKIES);
ini_set('session.use_trans_sid', SESSION_USE_TRANS_SID); // Desactiva el uso de IDs de sesión en URLs
ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY);

// Archivo de log de errores
ini_set('error_log', ERROR_LOG_FILE);
