<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Parse
{

    /**
     * Parse content.
     *
     * @param string $content the content
     *
     * @return $content (array)
     */
    protected function _parseContent(
        string $content
    ):string
    {
        $_content = '';
        $i = 0;
        foreach (explode(self::SEPARATOR, $content) as $c) {
            0 != $i++ and $_content .= $c;
        }
        $content = $_content;
        $content = str_replace('{Url}', self::urlBase(), $_content);
        $content = str_replace('{Email}', self::$config['email'], $content);
        $pos = strpos($content, '{More}');
        if (false === $pos) {
            $content = static::applyFilter('content', $content);
        } else {
            $content = explode('{More}', $content);
            $content['content_short'] = self::applyFilter('content', $content[0]);
            $content['content_full'] = self::applyFilter('content', $content[0] . $content[1]);
        }
        //$content = preg_replace('/\s+/', ' ', $content);
        $content = static::_evalPHP($content);

        return $content;
    }
}
