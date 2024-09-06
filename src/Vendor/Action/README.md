# AntCMS - Action

**AntCMS** incluye una clase llamada `Action` que permite definir y ejecutar acciones en diferentes partes del sistema. Este patrón es útil para extender funcionalidades de manera modular y reutilizable. Las acciones se pueden agregar con prioridad y luego ejecutarse cuando sea necesario.

## Requisitos

- **PHP** 7.4 o superior.
- Dependencia de la clase `Arr` ubicada en `src/Vendor/Arr/`.

## Instalación

Asegúrate de que el archivo `Action.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Action`.

```bash
src/
└───Vendor/
    └───Action/
        └───Action.php
```

## Uso de la clase Action

### Ejemplo básico de uso

La clase `Action` se puede usar para agregar y ejecutar acciones. A continuación un ejemplo básico:

```php
<?php

use Vendor\Action\Action;

// Agregar una acción llamada 'saludo'
Action::add('saludo', function($nombre) {
    echo "Hola, " . $nombre . "!";
});

// Ejecutar la acción 'saludo'
Action::run('saludo', ['Juan']);
```

En este ejemplo, se agrega una acción llamada `saludo` que toma un parámetro `$nombre` y luego se ejecuta la acción pasando el argumento `'Juan'`. La salida será:

```text
Hola, Juan!
```

### Métodos disponibles

#### 1. `add(string $name, callable $func, int $priority = 10, array $args = null): void`
Este método se usa para agregar una acción al sistema.

- **Parámetros**:
  - `$name`: El nombre de la acción.
  - `$func`: La función (callable) que se ejecutará cuando se llame a la acción.
  - `$priority`: La prioridad de la acción (por defecto 10). Las acciones con menor número de prioridad se ejecutan primero.
  - `$args`: Argumentos opcionales que se pasan a la función.

```php
Action::add('mi_accion', function() {
    echo 'Ejecutando mi acción.';
}, 5);
```

#### 2. `run(string $name, array $args = []): void`
Este método ejecuta todas las acciones asociadas a un nombre dado.

- **Parámetros**:
  - `$name`: El nombre de la acción a ejecutar.
  - `$args`: Un arreglo de argumentos que se pasarán a la función asociada.

```php
Action::run('mi_accion');
```

### Ejemplo avanzado

Puedes agregar múltiples acciones con el mismo nombre pero con diferentes prioridades. Las acciones con prioridad más baja se ejecutan primero.

```php
<?php

use Vendor\Action\Action;

// Agregar múltiples acciones con diferentes prioridades
Action::add('procesar', function() {
    echo "Acción con prioridad 10.<br>";
}, 10);

Action::add('procesar', function() {
    echo "Acción con prioridad 5.<br>";
}, 5);

// Ejecutar la acción 'procesar'
Action::run('procesar');
```

La salida será:
```text
Acción con prioridad 5.
Acción con prioridad 10.
```

### Ordenar las acciones

La clase `Action` utiliza la clase `Arr` para ordenar las acciones por prioridad antes de ejecutarlas. Asegúrate de tener la clase `Arr` correctamente configurada en `src/Vendor/Arr/`.

### Seguridad

La clase `Action` permite agregar acciones dinámicamente sin que se ejecuten inmediatamente, lo que ayuda a estructurar el código de manera más segura y predecible. Al controlar la ejecución de las acciones con prioridades, puedes garantizar que las funciones más importantes se ejecuten primero.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.
