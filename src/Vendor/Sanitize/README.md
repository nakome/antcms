# AntCMS - Sanitize

**AntCMS** incluye una clase llamada `Sanitize` que se utiliza para limpiar y sanitizar el contenido, garantizando que los datos no contengan etiquetas HTML o contenido malicioso. Esta clase ofrece métodos para sanitizar tanto cadenas de texto como archivos completos.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Sanitize.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Sanitize`.

```bash
src/
└───Vendor/
    └───Sanitize/
        └───Sanitize.php
```

## Uso de la clase Sanitize

### Ejemplo básico de uso

#### 1. Sanitizar contenido de texto

Para limpiar el contenido de una cadena de texto y eliminar etiquetas HTML no permitidas, puedes usar el método `content`.

```php
<?php

use Vendor\Sanitize\Sanitize;

// Cadena con contenido HTML potencialmente peligroso
$texto = "<script>alert('Ataque XSS');</script><p>Texto seguro</p>";

// Sanitizar el contenido
$textoSanitizado = Sanitize::content($texto);

echo $textoSanitizado;  // Salida: <p>Texto seguro</p>
```

En este ejemplo, el método elimina etiquetas peligrosas como `<script>`, dejando solo las permitidas como `<p>`.

#### 2. Sanitizar contenido de archivos

Puedes usar el método `file` para sanitizar el contenido de un archivo, eliminando las etiquetas HTML no deseadas.

```php
<?php

use Vendor\Sanitize\Sanitize;

// Ruta del archivo que contiene contenido HTML
$archivo = 'ruta/al/archivo.html';

// Sanitizar el contenido del archivo
$contenidoSanitizado = Sanitize::file($archivo);

echo $contenidoSanitizado;
```

Este método lee el contenido del archivo, lo sanitiza y lo devuelve sin etiquetas HTML peligrosas.

### Métodos disponibles

#### 1. `content(string $str): string`
Este método sanitiza una cadena de texto eliminando etiquetas HTML no permitidas.

- **Parámetros**:
  - `$str`: La cadena de texto que se desea sanitizar.
  
- **Devuelve**: El texto sanitizado, permitiendo solo etiquetas HTML seguras (especificadas en `ALLOWEDTAGS`).

```php
$textoSanitizado = Sanitize::content('<h1>Hola</h1><script>alert("XSS")</script>');
// Salida: <h1>Hola</h1>
```

#### 2. `file(string $filePath): string`
Este método lee el contenido de un archivo, lo sanitiza eliminando etiquetas HTML no deseadas, y lo devuelve.

- **Parámetros**:
  - `$filePath`: La ruta del archivo cuyo contenido debe ser sanitizado.
  
- **Devuelve**: El contenido del archivo sanitizado.

```php
$contenidoSanitizado = Sanitize::file('ruta/al/archivo.html');
```

### Ejemplo avanzado

Aquí tienes un ejemplo más avanzado donde se sanitiza tanto texto como el contenido de un archivo:

```php
<?php

use Vendor\Sanitize\Sanitize;

// Texto a sanitizar
$texto = "<div><strong>Texto seguro</strong><script>alert('Malicioso');</script></div>";
$textoSanitizado = Sanitize::content($texto);
echo $textoSanitizado;  // Salida: <div><strong>Texto seguro</strong></div>

// Sanitizar un archivo
$archivoSanitizado = Sanitize::file('ruta/al/archivo.html');
echo $archivoSanitizado;
```

En este ejemplo, tanto el texto como el contenido del archivo se sanitizan, eliminando cualquier etiqueta HTML peligrosa.

### Seguridad

La clase `Sanitize` es crucial para evitar ataques de **Cross-Site Scripting (XSS)** y otras vulnerabilidades relacionadas con la inserción de contenido malicioso. Sanitizando el contenido antes de mostrarlo en la página, se garantiza que solo las etiquetas HTML permitidas sean procesadas, protegiendo así al usuario.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.