<?php

declare (strict_types = 1);

namespace Vendor\Filter;

use function ksort;
use function array_slice;
use function array_merge;
use function call_user_func_array;

final class Filter
{

    protected static $_filters = [];

    /**
     * Aplicar filtro.
     *
     * @param string $filter_name Nombre del filtro a aplicar
     * @param string $value Valor a filtrar
     *
     * @return string Valor filtrado
     */
    public static function apply(string $filter_name, string $value): string
    {
        // Obtener argumentos adicionales
        $args = array_slice(func_get_args(), 2);

        // Si no existe el filtro, retornar el valor sin filtrar
        if (!isset(static::$_filters[$filter_name])) {
            return $value;
        }

        // Aplicar cada función del filtro en orden de prioridad
        foreach (static::$_filters[$filter_name] as $priority => $functions) {
            if (!is_null($functions)) {
                foreach ($functions as $function) {
                    $all_args      = array_merge([$value], $args);
                    $function_name = $function['function'];
                    $accepted_args = $function['accepted_args'];

                    // Preparar los argumentos para la llamada a la función
                    if (1 == $accepted_args) {
                        $the_args = [$value];
                    } elseif ($accepted_args > 1) {
                        $the_args = array_slice($all_args, 0, $accepted_args);
                    } elseif (0 == $accepted_args) {
                        $the_args = null;
                    } else {
                        $the_args = $all_args;
                    }

                    // Llamar a la función del filtro con los argumentos preparados
                    $value = call_user_func_array($function_name, $the_args);
                }
            }
        }

        // Retornar el valor filtrado
        return $value;
    }

    /**
     * Agrega una función como filtro a un nombre de filtro específico.
     *
     * @param string $filter_name     El nombre del filtro al que se agregará la función.
     * @param string $function_to_add La función que se agregará como filtro.
     * @param int    $priority        La prioridad del filtro en relación con otros filtros.
     * @param int    $accepted_args   El número de argumentos que la función de filtro puede aceptar.
     *
     * @return bool True si se agregó el filtro con éxito, false si no.
     */
    public static function set(string $filter_name, string $function_to_add, int $priority = 10, int $accepted_args = 1): bool
    {
        // Comprobar si ya se ha agregado el mismo filtro con la misma prioridad
        if (isset(static::$_filters[$filter_name]["$priority"])) {
            foreach (static::$_filters[$filter_name]["$priority"] as $filter) {
                if ($filter['function'] == $function_to_add) {
                    return true; // Ya se ha agregado la función, salir sin hacer nada
                }
            }
        }

        // Agregar la función al filtro
        static::$_filters[$filter_name]["$priority"][] = [
            'function'      => $function_to_add,
            'accepted_args' => $accepted_args,
        ];

        // Ordenar los filtros según la prioridad
        ksort(static::$_filters[$filter_name]["$priority"]);

        return true;
    }
}
