<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Run
{
    /**
     * Chain method.
     *
     * @return new static
     */
    public static function Run():object
    {
        return new static();
    }

}
