<?php

declare (strict_types = 1);

namespace AntCms;

defined('SECURE') or die('No tiene acceso al script.');

/**
 * AntCMS.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
interface IAntCMS
{

    /**
     * Creamos una action.
     *
     *  <code>
     *      AntCMS::actionAdd('demo',function(){});
     *  </code>
     *
     * @param string $name
     * @param mixed $func
     * @param int    $priority
     * @param array  $args
     *
     * @return static
     */
    public static function actionAdd(
        string $name,
        mixed $func,
        int $priority = 10,
        array $args = null
    ): void;

    /**
     * Llamamos una action.
     *
     *  <code>
     *      AntCMS::actionRun('demo',array());
     *  </code>
     *
     * @param string $name
     * @param array  $args
     *
     * @return void
     */
    public static function actionRun(
        string $name,
        array $args = []
    ): void;

    /**
     *  Array short.
     *
     * @param array $a      array
     * @param mixed $subkey mixed
     * @param mixed $order  mixed
     *
     * @return array
     */
    public static function shortArray(
        array $a = array(),
        string $subkey = "",
        string $order = null
    ): array;

    /**
     * C.O.R.S function.
     *
     * <code>
     *      AntCMS::cors();
     * </code>
     *
     * @return <type>
     */
    public static function cors(): void;

    /**
     * Obtiene Error en no publicado..
     *
     * @param object $Tpl
     *
     * @return void
     */
    public function errorPage(object $Tpl): void;

    /**
     * Aplicar filtro.
     *
     * @param string $filter_name
     * @param string $value
     *
     * @return string
     */
    public static function applyFilter(
        string $filter_name,
        string $value
    ): string;

    /**
     *  Obtener array de páginas.
     *
     * <code>
     *  AntCMS::pages('blog','date','DESC',array('index','404'),null);
     * </code>
     *
     * @param string $url
     * @param string $order_by
     * @param string $order_type
     * @param array  $ignore
     * @param int    $limit
     *
     * @return array
     */
    public function pages(
        string $url,
        string $order_by = 'date',
        string $order_type = 'DESC',
        array $ignore = ['404'],
        int $limit = 0
    ): array;

    /**
     * Escanear carpeta
     *
     * <code>
     *  AntCMS::scanFiles(CONTENT,'md',false);
     * </code>
     *
     * @param string $folder
     * @param string $type
     * @param bool   $file_path
     *
     * @return array
     */
    public static function scanFiles(
        string $folder,
        string $type = 'html',
        bool $file_path = true
    ): array;

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
    ): string;

    /**
     * Obtener url base
     *
     * <code>
     *   AntCMS::urlBase();
     * </code>
     *
     * @return string url
     */
    public static function urlBase(): string;

    /**
     * Obtener url actual
     *
     * <code>
     *  AntCMS::urlCurrent();
     * </code>
     *
     * @return string
     */
    public static function urlCurrent(): string;

    /**
     * Segmentos url
     *
     * <code>
     *  AntCMS::urlSegments();
     * </code>
     *
     * @return array
     */
    public static function urlSegments(): array;

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
    ): string;

    /**
     *  Sanitizar Url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function urlSanitize(
        string $url
    ): string;

    /**
     * Corremos la sanitización.
     *
     * @return void
     */
    public static function runSanitize(): void;

    // trait Para funciones estaticas
    public static function Run(): object;

    /**
     * Obtener archivos
     *
     * @param string $name
     *
     * @return void
     */
    public static function getFile(
        string $name
    ): void;

    /**
     * Iniciar AntCMS.
     *
     * @param string $path
     *
     * @return void
     */
    public function init(
        string $configFile,
        string $templateConfigFile
    ): void;
}
