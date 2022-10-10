<?php

declare (strict_types = 1);

namespace Functions;

defined('SECURE') or die('No tiene acceso al script.');

use function strlen;
use function mt_rand;

interface IFunctions {
    /**
     * Retorna static
     *
     * @return static
     */
    public static function run():self;

    /**
     * Cargando las funciones
     *
     * @return void
     */
    public static function init(): void;

    /**
     * Flecha izq.
     *
     * @return string
     */
    public function leftArrow(): string;

    /**
     * Flecha drch.
     *
     * @return string
     */
    public function rightArrow(): string;

    /**
     * Articles
     *
     * @param string $name
     * @param int $num
     *
     * @return void
     */
    public static function articles(string $name,int $num): void;

    /**
     * Navegación
     *
     * @return void
     */
    public static function menu(): void;

    /**
     * Metodo navFolder
     *
     * @return void
     */
    public static function navFolder(): void;


    /**
     * Buscar
     *
     * @return void
     */
    public static function query(): void;

    /**
     * Contacto
     * 
     * @return void
     */
    public static function contact(): void;

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
    ):string;
}