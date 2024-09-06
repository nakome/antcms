<?php

declare (strict_types = 1);

namespace Vendor\Action;

use Vendor\Arr\Arr as Arr;

final class Action
{

    private static $__actions = [];

    /**
     * Creamos una action.
     *
     *  <code>
     *      Ant\FrontEnd\FrontEnd::actionAdd('demo',function(){});
     *  </code>
     *
     * @param string $name
     * @param mixed  $func
     * @param int    $priority
     * @param array  $args
     *
     * @return static
     */
    public static function add(string $name, callable $func, int $priority = 10, array $args = null): void
    {
        // Agregamos la funcion
        static::$__actions[] = [
            'name'     => $name,
            'func'     => $func,
            'priority' => $priority,
            'args'     => $args,
        ];
    }

    /**
     * Llamamos una action.
     *
     *  <code>
     *      Ant\FrontEnd\FrontEnd::actionRun('demo',array());
     *  </code>
     *
     * @param string $name
     * @param array  $args
     *
     * @return void
     */
    public static function run(string $name, array $args = []): void
    {
        if (count(static::$__actions) > 0) {
            // Ordenar las acciones por prioridad
            $actions = Arr::sort(static::$__actions, 'priority');
            // Bucle a través de $actions matriz
            foreach ($actions as $action) {
                // Ejecutar una acción específica
                if ($action['name'] == $name) {
                    // isset argumentos ?
                    if (isset($args)) {
                        // Devolver o representar resultados de acciones específicas
                        call_user_func_array($action['func'], $args);
                    } else {
                        call_user_func_array($action['func'], $action['args']);
                    }
                }
            }
        }
    }
}
