Aquí tienes un ejemplo de `README.md` que incluye la clase `Session` junto con ejemplos claros de uso.

```md
# AntCMS - Session

**AntCMS** incluye una clase llamada `Session` que facilita el manejo de sesiones de manera segura y eficiente. Esta clase proporciona métodos para iniciar sesiones, establecer y obtener valores de sesión, verificar la existencia de claves, y eliminar sesiones.

## Requisitos

- **PHP** 7.4 o superior.
- Extensión **session** habilitada en PHP.

## Instalación

Asegúrate de que el archivo `Session.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Session`.

```bash
src/
└───Vendor/
    └───Session/
        └───Session.php
```

## Uso de la clase Session

### Ejemplo básico de uso

La clase `Session` se puede usar para manejar sesiones de usuario de manera sencilla. A continuación, un ejemplo básico de cómo iniciar una sesión, establecer y obtener valores:

```php
<?php

use Vendor\Session\Session;

// Iniciar la sesión
Session::start();

// Establecer un valor en la sesión
Session::set('usuario', 'John Doe');

// Obtener un valor de la sesión
$usuario = Session::get('usuario');
echo $usuario; // Salida: John Doe

// Verificar si un valor de sesión existe
if (Session::exists('usuario')) {
    echo 'El usuario está presente en la sesión.';
}

// Eliminar un valor de la sesión
Session::delete('usuario');

// Destruir toda la sesión
Session::destroy();
```

### Métodos disponibles

#### 1. `start(): bool`
Inicia la sesión si aún no ha sido iniciada.

- **Devuelve**: `true` si la sesión ya estaba iniciada o si se inicia correctamente, `false` si no se pudo iniciar.

```php
Session::start();
```

#### 2. `set(string $key, mixed $value): void`
Establece un valor en la sesión.

- **Parámetros**:
  - `$key`: La clave de la sesión.
  - `$value`: El valor a almacenar.

```php
Session::set('usuario', 'John Doe');
```

#### 3. `get(string $key): mixed`
Obtiene el valor de una clave de la sesión.

- **Parámetro**:
  - `$key`: La clave de la sesión a obtener.
- **Devuelve**: El valor de la clave o `null` si no existe.

```php
$usuario = Session::get('usuario');
```

#### 4. `exists(string ...$keys): bool`
Verifica si todas las claves proporcionadas existen en la sesión.

- **Parámetros**:
  - `...$keys`: Una lista de claves a verificar.
- **Devuelve**: `true` si todas las claves existen, `false` si alguna falta.

```php
if (Session::exists('usuario', 'email')) {
    echo 'Todos los valores existen en la sesión.';
}
```

#### 5. `delete(mixed ...$args): void`
Elimina uno o varios valores de la sesión.

- **Parámetros**:
  - `...$args`: Las claves de los valores de sesión a eliminar. Se pueden pasar como argumentos separados o como un arreglo.

```php
Session::delete('usuario', 'email');  // Eliminar múltiples claves
Session::delete(['usuario', 'email']);  // Pasar un array para eliminar
```

#### 6. `destroy(): void`
Destruye toda la sesión y elimina todas las variables de sesión.

```php
Session::destroy();
```

### Seguridad

Esta clase maneja sesiones de manera eficiente y segura, asegurando que los valores de sesión solo estén disponibles cuando la sesión ha sido iniciada, y permite eliminar claves individuales o destruir completamente la sesión cuando sea necesario.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.
