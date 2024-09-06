# AntCMS - PasswordHasher

**AntCMS** incluye una clase llamada `PasswordHasher` que permite gestionar de manera segura las contraseñas de los usuarios. Esta clase proporciona funciones para hashear contraseñas, verificarlas y comprobar si un hash necesita ser actualizado.

## Requisitos

- **PHP** 7.4 o superior.
- Extensión **password_hash** disponible en PHP.

## Instalación

Asegúrate de que el archivo `PasswordHasher.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/PasswordHasher`.

```bash
src/
└───Vendor/
    └───PasswordHasher/
        └───PasswordHasher.php
```

## Uso de PasswordHasher

### Ejemplo básico de uso

La clase `PasswordHasher` se utiliza para hashear y verificar contraseñas de manera sencilla. Aquí tienes un ejemplo básico de cómo usarla:

```php
<?php

use Vendor\PasswordHasher\PasswordHasher;

// Crear una instancia de PasswordHasher con Bcrypt y un costo de 12
$hasher = new PasswordHasher(PASSWORD_BCRYPT, ['cost' => 12]);

// Hashear una contraseña
$hashedPassword = $hasher->hash('mi-contraseña-segura');

// Verificar si la contraseña es válida
$isPasswordValid = $hasher->verify('mi-contraseña-segura', $hashedPassword);

if ($isPasswordValid) {
    echo 'Contraseña verificada correctamente.';
} else {
    echo 'La contraseña no es válida.';
}
```

### Métodos disponibles

#### 1. `hash(string $password): string`
Este método genera el hash de una contraseña utilizando el algoritmo especificado.

- **Parámetro**:
  - `$password`: La contraseña a hashear.
- **Devuelve**: El hash de la contraseña.

```php
$hashedPassword = $hasher->hash('mi-contraseña-segura');
```

#### 2. `verify(string $password, string $hash): bool`
Verifica si la contraseña proporcionada coincide con el hash almacenado.

- **Parámetros**:
  - `$password`: La contraseña sin hashear.
  - `$hash`: El hash de la contraseña almacenada.
- **Devuelve**: `true` si la contraseña es válida, `false` si no lo es.

```php
$isPasswordValid = $hasher->verify('mi-contraseña-segura', $hashedPassword);
```

#### 3. `needsRehash(string $hash): bool`
Comprueba si el hash necesita ser actualizado según los parámetros actuales del algoritmo de hash.

- **Parámetro**:
  - `$hash`: El hash de la contraseña almacenada.
- **Devuelve**: `true` si el hash necesita ser rehasheado, `false` si no lo necesita.

```php
if ($hasher->needsRehash($hashedPassword)) {
    // Rehash de la contraseña
    $hashedPassword = $hasher->hash('mi-contraseña-segura');
}
```

### Opciones de configuración

Al crear una instancia de `PasswordHasher`, puedes especificar diferentes algoritmos y opciones. Aquí se usa `PASSWORD_BCRYPT` con un costo de 12:

```php
$hasher = new PasswordHasher(PASSWORD_BCRYPT, ['cost' => 12]);
```

O puedes usar el valor predeterminado:

```php
$hasher = new PasswordHasher(); // Usa PASSWORD_DEFAULT
```

## Seguridad

Este sistema de hashing utiliza los algoritmos nativos de PHP como `PASSWORD_BCRYPT` y es compatible con futuras mejoras de seguridad al permitir la actualización de los hashes mediante el método `needsRehash`. Esto asegura que las contraseñas estén protegidas con las mejores prácticas actuales.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.
