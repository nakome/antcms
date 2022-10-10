<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Arr
{
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
    ): array
    {
        if (count($a) != 0 || (!empty($a))) {
            foreach ($a as $k => $v) {
                // si resulta ser string convertir a minúsculas
                if (is_string($v[$subkey])) {
                    $b[$k] = strtolower((string)$v[$subkey]);
                } else {
                    $b[$k] = $v[$subkey];
                }
            }
            // Orden ascendente
            if ($order == null || $order == 'ASC') {
                asort($b);
                // Orden descendente
            } elseif ($order == 'DESC') {
                arsort($b);
            }

            foreach ($b as $key => $val) {
                $c[] = $a[$key];
            }

            return $c;
        }
    }
}
