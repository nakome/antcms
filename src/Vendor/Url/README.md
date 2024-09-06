# AntCMS - Url

**AntCMS** incluye una clase llamada `Url` que proporciona métodos útiles para manejar URLs de manera eficiente y flexible. Esta clase permite obtener la URL base de la aplicación, la URL actual, y realizar modificaciones en las URLs.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Url.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Url`.

```bash
src/
└───Vendor/
    └───Url/
        └───Url.php
```

## Uso de la clase Url

### Ejemplo básico de uso

#### 1. Obtener la URL base

Puedes usar el método `base` para obtener la URL base de la aplicación. Este método detecta automáticamente si la aplicación se está ejecutando en un entorno local o en un servidor remoto y ajusta la URL base según corresponda.

```php
<?php

use Vendor\Url\Url;

// Obtener la URL base
$baseUrl = Url::base();

echo $baseUrl;
```

Este código devuelve la URL base de la aplicación, como `http://localhost/misitio` o `https://www.ejemplo.com`.

#### 2. Obtener la URL actual

El método `current` te permite obtener la URL completa de la página actual en la que se encuentra el usuario.

```php
<?php

use Vendor\Url\Url;

// Obtener la URL actual
$currentUrl = Url::current();

echo $currentUrl;
```

Este código imprime la URL completa de la página actual, por ejemplo, `https://www.ejemplo.com/productos`.

#### 3. Parsear una URL

El método `parse` reemplaza la ruta de contenido con la URL del sitio y limpia la URL eliminando `.html` o `index.html` al final.

```php
<?php

use Vendor\Url\Url;

// Parsear una URL
$siteUrl = 'https://www.ejemplo.com';
$page = '/content/page.html';

$parsedUrl = Url::parse($siteUrl, $page);

echo $parsedUrl;  // Salida: https://www.ejemplo.com/page
```

Este ejemplo muestra cómo limpiar y convertir una ruta interna en una URL pública amigable.

### Métodos disponibles

#### 1. `base(): string`
Este método obtiene la URL base de la aplicación. Detecta si la aplicación se ejecuta en localhost o en un servidor remoto y ajusta la URL según corresponda.

- **Devuelve**: La URL base de la aplicación.

```php
$baseUrl = Url::base();
```

#### 2. `current(): string`
Este método devuelve la URL completa de la página actual en la que se encuentra el usuario.

- **Devuelve**: La URL completa actual.

```php
$currentUrl = Url::current();
```

#### 3. `parse(string $site_url, string $page): string`
Este método limpia y ajusta una URL interna para convertirla en una URL pública amigable.

- **Parámetros**:
  - `$site_url`: La URL base del sitio.
  - `$page`: La URL interna o la ruta de la página.
  
- **Devuelve**: La URL pública limpia y amigable.

```php
$parsedUrl = Url::parse('https://www.ejemplo.com', '/content/page.html');
```

### Ejemplo avanzado

Aquí tienes un ejemplo donde se obtienen varias URLs en diferentes contextos:

```php
<?php

use Vendor\Url\Url;

// Obtener la URL base
$baseUrl = Url::base();
echo "URL base: " . $baseUrl . "<br>";

// Obtener la URL actual
$currentUrl = Url::current();
echo "URL actual: " . $currentUrl . "<br>";

// Parsear una URL interna para convertirla en una URL pública
$parsedUrl = Url::parse($baseUrl, '/content/contact.html');
echo "URL pública: " . $parsedUrl;
```

Este ejemplo muestra cómo obtener la URL base, la URL actual y cómo convertir una ruta interna en una URL pública amigable.

### Seguridad

La clase `Url` se asegura de generar y manejar URLs de manera segura, eliminando caracteres especiales y asegurando que las URLs sean consistentes y válidas tanto en entornos locales como remotos. Además, los métodos utilizados permiten limpiar URLs, eliminando contenido potencialmente peligroso.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.