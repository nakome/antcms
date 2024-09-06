# AntCMS - Arr

**AntCMS** incluye una clase llamada `Arr` que proporciona utilidades para manipular arreglos de manera eficiente. Actualmente, la clase incluye un método para ordenar un arreglo en función de una clave específica, con soporte para orden ascendente o descendente.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Arr.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Arr`.

```bash
src/
└───Vendor/
    └───Arr/
        └───Arr.php
```

## Uso de la clase Arr

### Ejemplo básico de uso

La clase `Arr` se puede usar para ordenar arreglos por una clave específica. Aquí tienes un ejemplo básico:

```php
<?php

use Vendor\Arr\Arr;

// Crear un arreglo de ejemplo
$datos = [
    ['nombre' => 'Juan', 'edad' => 25],
    ['nombre' => 'Ana', 'edad' => 30],
    ['nombre' => 'Pedro', 'edad' => 22],
];

// Ordenar el arreglo por la clave 'edad' en orden ascendente
$ordenado = Arr::sort($datos, 'edad', 'ASC');

print_r($ordenado);
```

### Métodos disponibles

#### 1. `sort(array $a, string $subkey, string $order = 'ASC'): array`
Este método ordena un arreglo en función de una clave específica, permitiendo la opción de orden ascendente (`ASC`, valor por defecto) o descendente (`DESC`).

- **Parámetros**:
  - `$a`: El arreglo a ordenar.
  - `$subkey`: La clave del arreglo que se usará para ordenar.
  - `$order`: El orden de la ordenación (`ASC` para ascendente, `DESC` para descendente).

- **Devuelve**: Un nuevo arreglo ordenado.

```php
$ordenadoAsc = Arr::sort($datos, 'edad', 'ASC');
$ordenadoDesc = Arr::sort($datos, 'edad', 'DESC');
```

### Ejemplo avanzado

Aquí tienes un ejemplo más avanzado donde ordenamos un arreglo de personas por su nombre en orden descendente.

```php
<?php

use Vendor\Arr\Arr;

$personas = [
    ['nombre' => 'Carlos', 'edad' => 34],
    ['nombre' => 'Beatriz', 'edad' => 28],
    ['nombre' => 'Andrés', 'edad' => 45],
];

// Ordenar por la clave 'nombre' en orden descendente
$ordenado = Arr::sort($personas, 'nombre', 'DESC');

print_r($ordenado);
```

La salida será:

```php
Array
(
    [0] => Array
        (
            [nombre] => Carlos
            [edad] => 34
        )

    [1] => Array
        (
            [nombre] => Beatriz
            [edad] => 28
        )

    [2] => Array
        (
            [nombre] => Andrés
            [edad] => 45
        )
)
```

### Seguridad

La clase `Arr` ofrece una forma segura y eficiente de ordenar arreglos, asegurándose de manejar cadenas y otros tipos de datos de manera adecuada para evitar errores en la ordenación.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.