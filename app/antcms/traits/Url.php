<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Url
{
    /**
     * Obtener url base
     *
     * <code>
     *   AntCMS::urlBase();
     * </code>
     *
     * @return string url
     */
    public static function urlBase(): string
    {
        $whitelist = [
            '127.0.0.1',
            '::1',
        ];

        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
            return $https . rtrim(rtrim($_SERVER['HTTP_HOST'], '\\/') . dirname($_SERVER['PHP_SELF']), '\\/');
        } else {
            return static::$config['url'];
        }
    }

    /**
     * Obtener url actual
     *
     * <code>
     *  AntCMS::urlCurrent();
     * </code>
     *
     * @return string
     */
    public static function urlCurrent(): string
    {
        $url = '';
        $request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script_url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
        if ($request_url != $script_url) {
            $url = trim(preg_replace('/' . str_replace('/', '\\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
        }
        $url = preg_replace('/\\?.*/', '', $url);

        return $url;
    }

    /**
     * Segmentos url
     *
     * <code>
     *  AntCMS::urlSegments();
     * </code>
     *
     * @return array
     */
    public static function urlSegments(): array
    {
        return explode('/', self::urlCurrent());
    }

    /**
     * Segmento url
     *
     * <code>
     *  AntCMS::urlSegment(1);
     * </code>
     *
     * @param int $num
     *
     * @return string
     */
    public static function urlSegment(
        int $num = 0
    ): string{
        $segments = self::UrlSegments();
        return isset($segments[$num]) ? $segments[$num] : '';
    }

    /**
     *  Sanitizar Url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function urlSanitize(
        string $url
    ): string{
        $url = trim($url);
        $url = rawurldecode($url);
        $url = str_replace(['--', '&quot;', '!', '@', '#', '$', '%', '^', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '[', ']', '\\', ';', "'", ',', '*', '+', '~', '`', 'laquo', 'raquo', ']>', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8211;', '&#8212;'], ['-', '-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], $url);
        $url = str_replace('--', '-', $url);
        $url = rtrim($url, '-');
        $url = str_replace('..', '', $url);
        $url = str_replace('//', '', $url);
        $url = preg_replace('/^\//', '', $url);
        $url = preg_replace('/^\./', '', $url);

        return $url;
    }

    /**
     * Corremos la sanitización.
     *
     * @return void
     */
    public static function runSanitize(): void
    {
        $_GET = array_map(['AntCms\AntCMS', 'urlSanitize'], $_GET);
    }

}
