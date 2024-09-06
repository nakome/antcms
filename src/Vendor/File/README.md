# AntCMS - File

**AntCMS** incluye una clase llamada `File` que proporciona métodos útiles para manejar archivos de manera segura y eficiente. La clase permite obtener el contenido de archivos y escanear directorios en busca de archivos específicos.

## Requisitos

- **PHP** 7.4 o superior.
- Dependencia de la clase `Sanitize` ubicada en `src/Vendor/Sanitize/`.

## Instalación

Asegúrate de que el archivo `File.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/File`.

```bash
src/
└───Vendor/
    └───File/
        └───File.php
```

## Uso de la clase File

### Ejemplo básico de uso

#### 1. Obtener el contenido de un archivo

Puedes usar el método `get` para obtener y mostrar el contenido de un archivo:

```php
<?php

use Vendor\File\File;

// Obtener y mostrar el contenido de un archivo llamado 'example.txt'
try {
    File::get('example.txt');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

Si el archivo existe, su contenido se imprimirá en pantalla. Si no existe, se lanzará una excepción con un mensaje de error.

#### 2. Escanear un directorio en busca de archivos de un tipo específico

Puedes usar el método `scan` para buscar archivos de un tipo específico en un directorio:

```php
<?php

use Vendor\File\File;

// Escanear el directorio 'public/content' en busca de archivos '.html'
$archivos = File::scan('public/content', 'html');

// Mostrar los archivos encontrados
print_r($archivos);
```

Este ejemplo escanea el directorio `public/content` en busca de archivos con la extensión `.html` y devuelve un arreglo con las rutas completas de los archivos encontrados.

### Métodos disponibles

#### 1. `get(string $name): void`
Obtiene y muestra el contenido de un archivo.

- **Parámetros**:
  - `$name`: El nombre (y ruta) del archivo a obtener.
- **Devuelve**: Nada. Imprime el contenido del archivo directamente o lanza una excepción si el archivo no existe.

```php
try {
    File::get('ruta/al/archivo.txt');
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### 2. `scan(string $folder, string $type = 'html', bool $file_path = true): array|false`
Escanea un directorio en busca de archivos de un tipo específico y devuelve una lista de archivos que coinciden con ese tipo.

- **Parámetros**:
  - `$folder`: El directorio a escanear.
  - `$type`: El tipo de archivo que se va a buscar (por defecto `'html'`).
  - `$file_path`: Si `true`, devuelve la ruta completa del archivo; si `false`, solo devuelve el nombre del archivo.
- **Devuelve**: Un arreglo de archivos encontrados o `false` si el directorio no existe.

```php
$archivos = File::scan('public/images', 'jpg', false);
```

Este ejemplo buscará archivos `.jpg` en el directorio `public/images` y devolverá solo los nombres de los archivos.

### Ejemplo avanzado

Aquí tienes un ejemplo más avanzado que busca múltiples tipos de archivos en un directorio:

```php
<?php

use Vendor\File\File;

// Buscar archivos '.html' y '.php' en el directorio 'src'
$archivos = File::scan('src', ['html', 'php'], true);

print_r($archivos);
```

Este ejemplo buscará archivos con las extensiones `.html` y `.php` en el directorio `src`, y devolverá un arreglo con las rutas completas de los archivos encontrados.

### Seguridad

La clase `File` se asegura de que solo se lean archivos que existen y que son archivos regulares. Además, utiliza la clase `Sanitize` para limpiar el contenido del archivo antes de mostrarlo, evitando posibles ataques de inyección de archivos o contenido malicioso.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.