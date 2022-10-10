<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait EvalPhp
{
    /**
     * Eval Php.
     *
     * @param string $str
     *
     * @return string
     */
    protected static function _evalPHP(
        string $str
    ): string {
        return preg_replace_callback('/\\{php\\}(.*?)\\{\\/php\\}/ms', 'AntCMS\AntCMS::_obEval', $str);
    }

    /**
     * Eval Content.
     *
     * @param string $data the data
     *
     * @return string
     */
    protected static function _obEval(string $data): string
    {
        ob_start();
        eval($data[1]);
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

}
