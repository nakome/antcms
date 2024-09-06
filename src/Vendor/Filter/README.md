# AntCMS - Filter

**AntCMS** incluye una clase llamada `Filter` que permite agregar y aplicar filtros a valores de manera flexible y modular. Los filtros son funciones que se pueden aplicar en varios puntos del código para modificar o limpiar valores de manera controlada.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Filter.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Filter`.

```bash
src/
└───Vendor/
    └───Filter/
        └───Filter.php
```

## Uso de la clase Filter

### Ejemplo básico de uso

#### 1. Agregar un filtro

Puedes usar el método `set` para agregar una función que actúe como filtro. Esta función se ejecutará cuando el filtro correspondiente sea aplicado.

```php
<?php

use Vendor\Filter\Filter;

// Definir una función que se usará como filtro
function convertirMayusculas($valor) {
    return strtoupper($valor);
}

// Agregar el filtro 'convertir_mayusculas' con prioridad 10
Filter::set('convertir_mayusculas', 'convertirMayusculas', 10);
```

#### 2. Aplicar un filtro

Una vez que has definido un filtro, puedes usar el método `apply` para aplicarlo a un valor específico.

```php
<?php

use Vendor\Filter\Filter;

// Aplicar el filtro 'convertir_mayusculas' a un valor
$valorFiltrado = Filter::apply('convertir_mayusculas', 'Hola Mundo');

echo $valorFiltrado;  // Salida: HOLA MUNDO
```

### Métodos disponibles

#### 1. `set(string $filter_name, string $function_to_add, int $priority = 10, int $accepted_args = 1): bool`
Este método permite agregar una función como filtro a un nombre de filtro específico.

- **Parámetros**:
  - `$filter_name`: El nombre del filtro al que se agregará la función.
  - `$function_to_add`: El nombre de la función que actuará como filtro.
  - `$priority`: La prioridad del filtro (por defecto 10). Los filtros con menor prioridad se ejecutan primero.
  - `$accepted_args`: El número de argumentos que la función de filtro puede aceptar.
  
- **Devuelve**: `true` si se agregó el filtro con éxito, `false` si no.

```php
Filter::set('mi_filtro', 'miFuncionFiltro', 5);
```

#### 2. `apply(string $filter_name, string $value): string`
Este método aplica el filtro al valor proporcionado. Si no se ha definido un filtro con ese nombre, el valor original será retornado sin modificar.

- **Parámetros**:
  - `$filter_name`: El nombre del filtro que se va a aplicar.
  - `$value`: El valor que será procesado por el filtro.
  
- **Devuelve**: El valor filtrado.

```php
$valorFiltrado = Filter::apply('mi_filtro', 'Valor a filtrar');
```

### Ejemplo avanzado

Puedes agregar múltiples funciones a un mismo filtro con diferentes prioridades, lo que permite modificar el valor en varias etapas.

```php
<?php

use Vendor\Filter\Filter;

// Filtro para convertir a mayúsculas
function convertirMayusculas($valor) {
    return strtoupper($valor);
}

// Filtro para agregar un sufijo
function agregarSufijo($valor) {
    return $valor . '!!!';
}

// Agregar los filtros
Filter::set('modificar_texto', 'convertirMayusculas', 10);
Filter::set('modificar_texto', 'agregarSufijo', 20);

// Aplicar el filtro al valor
$textoModificado = Filter::apply('modificar_texto', 'Hola Mundo');

echo $textoModificado;  // Salida: HOLA MUNDO!!!
```

En este ejemplo, el filtro `modificar_texto` aplica primero la función `convertirMayusculas` y luego la función `agregarSufijo`, siguiendo el orden de prioridad establecido.

### Seguridad

La clase `Filter` permite modificar valores de manera controlada a través de funciones predefinidas. Al utilizar funciones para limpiar o procesar datos, se pueden evitar riesgos comunes como la inyección de datos o la manipulación inesperada de valores.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.