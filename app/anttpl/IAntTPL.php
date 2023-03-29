<?php

declare (strict_types = 1);

namespace AntTpl;

defined('SECURE') or die('No tiene acceso al script.');

interface IAntTPL
{

    /**
     * Remove cache 1 day
     *
     * @return void
     */
    public function removeCacheOneDay():void;
    
    /**
     * Callback.
     *
     * @param mixed $variable the var
     *
     * @return array|string
     */
    public function callback($variable);

    /**
     *  Set var.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return mixed
     */
    public function set(string $name, $value): object;

    /**
     * Append data in array.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return null
     */
    public function append($name, $value);

    /**
     * Draw file.
     *
     * @param string $file the file
     *
     * @return string
     */
    public function draw(string $file): string;

    /**
     *  Comment.
     *
     * @param string $content the content
     *
     * @return string
     */
    public function comment(string $content): string;

    /**
     * Minify html
     *
     * @param string $input
     *
     * @return string
     */
    public function minify_html(string $input): string;

    /**
     * Minify css
     *
     * @param string $input
     *
     * @return string
     */
    public function minify_css(string $input): string;

    /**
     * Minify js
     *
     * @param string $input
     *
     * @return string
     */
    public function minify_js(string $input): string;
}
