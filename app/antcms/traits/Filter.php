<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

trait Filter
{

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
    ): string {
        // Redefinir argumentos
        $args = array_slice(func_get_args(), 2);
        if (!isset(static::$_filters[$filter_name])) {
            return $value;
        }
        foreach (static::$_filters[$filter_name] as $priority => $functions) {
            if (!is_null($functions)) {
                foreach ($functions as $function) {
                    $all_args = array_merge([$value], $args);
                    $function_name = $function['function'];
                    $accepted_args = $function['accepted_args'];
                    if (1 == $accepted_args) {
                        $the_args = [$value];
                    } elseif ($accepted_args > 1) {
                        $the_args = array_slice($all_args, 0, $accepted_args);
                    } elseif (0 == $accepted_args) {
                        $the_args = null;
                    } else {
                        $the_args = $all_args;
                    }
                    $value = call_user_func_array($function_name, $the_args);
                }
            }
        }

        return $value;
    }

    /**
     * crear filtro.
     *
     * @param string $filter_name
     * @param string $function_to_add
     * @param int    $priority
     * @param int    $accepted_args
     *
     * @return bool
     */
    public static function setFilter(
        string $filter_name,
        string $function_to_add,
        int $priority = 10,
        int $accepted_args = 1
    ): bool {
        // Compruebe que no tenemos ya el mismo filtro con la misma prioridad. Gracias a WP :)
        if (isset(static::$_filters[$filter_name]["$priority"])) {
            foreach (static::$_filters[$filter_name]["$priority"] as $filter) {
                if ($filter['function'] == $function_to_add) {
                    return true;
                }
            }
        }
        static::$_filters[$filter_name]["$priority"][] = ['function' => $function_to_add, 'accepted_args' => $accepted_args];
        // Sort
        ksort(static::$_filters[$filter_name]["$priority"]);

        return true;
    }
}
