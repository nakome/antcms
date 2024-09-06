# AntCMS - Form

**AntCMS** incluye una clase llamada `Form` que facilita la creación dinámica de formularios HTML. Esta clase permite generar inputs, checkboxes, textarea, campos ocultos y botones de envío de manera eficiente, utilizando arreglos de configuración para personalizar cada campo.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Form.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Form`.

```bash
src/
└───Vendor/
    └───Form/
        └───Form.php
```

## Uso de la clase Form

### Ejemplo básico de uso

#### 1. Generar un campo de texto

Puedes generar un input tipo texto utilizando el método `input` de la clase `Form`.

```php
<?php

use Vendor\Form\Form;

// Crear un campo de texto
echo Form::input([
    'name' => 'username',
    'label' => 'Nombre de Usuario',
    'placeholder' => 'Ingrese su nombre',
    'required' => true,
    'class' => 'form-control'
]);
```

Este código generará un campo de texto con la etiqueta "Nombre de Usuario" y el placeholder "Ingrese su nombre".

#### 2. Generar un checkbox

Para crear un checkbox, utiliza el método `checkbox`.

```php
<?php

use Vendor\Form\Form;

// Crear un checkbox
echo Form::checkbox([
    'name' => 'terms',
    'label' => 'Acepto los términos y condiciones',
    'required' => true,
    'class' => 'form-check-input'
]);
```

#### 3. Generar un botón de envío

Puedes crear un botón de envío con el método `submit`.

```php
<?php

use Vendor\Form\Form;

// Crear un botón de envío
echo Form::submit([
    'name' => 'submit',
    'value' => 'Enviar',
    'class' => 'btn btn-primary'
]);
```

### Métodos disponibles

#### 1. `input(array $args): string`
Genera un campo de texto o número.

- **Parámetros**:
  - `$args`: Un arreglo con las configuraciones del input, incluyendo `name`, `label`, `placeholder`, `type`, `required`, `value`, y `class`.
- **Devuelve**: El HTML del campo de texto.

```php
echo Form::input([
    'name' => 'email',
    'label' => 'Correo electrónico',
    'type' => 'email',
    'placeholder' => 'Ingrese su correo',
    'required' => true,
    'class' => 'form-control'
]);
```

#### 2. `checkbox(array $args): string`
Genera un checkbox.

- **Parámetros**:
  - `$args`: Un arreglo con las configuraciones del checkbox, incluyendo `name`, `label`, `required`, `value`, y `class`.
- **Devuelve**: El HTML del checkbox.

```php
echo Form::checkbox([
    'name' => 'newsletter',
    'label' => 'Suscribirme al boletín',
    'value' => '1',
    'class' => 'form-check-input'
]);
```

#### 3. `textarea(array $args): string`
Genera un campo de texto multilínea (textarea).

- **Parámetros**:
  - `$args`: Un arreglo con las configuraciones del textarea, incluyendo `name`, `label`, `placeholder`, `rows`, `required`, y `value`.
- **Devuelve**: El HTML del textarea.

```php
echo Form::textarea([
    'name' => 'message',
    'label' => 'Mensaje',
    'placeholder' => 'Escribe tu mensaje aquí...',
    'rows' => 5,
    'required' => true,
    'value' => ''
]);
```

#### 4. `hidden(array $args): string`
Genera un campo oculto.

- **Parámetros**:
  - `$args`: Un arreglo con las configuraciones del campo oculto, incluyendo `name` y `value`.
- **Devuelve**: El HTML del campo oculto.

```php
echo Form::hidden([
    'name' => 'token',
    'value' => 'abc123'
]);
```

#### 5. `submit(array $args): string`
Genera un botón de envío.

- **Parámetros**:
  - `$args`: Un arreglo con las configuraciones del botón, incluyendo `name`, `value`, y `class`.
- **Devuelve**: El HTML del botón de envío.

```php
echo Form::submit([
    'name' => 'submit',
    'value' => 'Enviar',
    'class' => 'btn btn-success'
]);
```

### Ejemplo avanzado

A continuación, un ejemplo de cómo generar un formulario completo utilizando la clase `Form`.

```php
<?php

use Vendor\Form\Form;

// Generar un formulario completo
echo Form::input([
    'name' => 'username',
    'label' => 'Nombre de Usuario',
    'placeholder' => 'Ingrese su nombre',
    'required' => true,
    'class' => 'form-control'
]);

echo Form::textarea([
    'name' => 'bio',
    'label' => 'Biografía',
    'placeholder' => 'Escribe sobre ti...',
    'rows' => 3,
    'required' => true
]);

echo Form::checkbox([
    'name' => 'agree',
    'label' => 'Acepto los términos',
    'required' => true,
    'class' => 'form-check-input'
]);

echo Form::submit([
    'name' => 'submit',
    'value' => 'Registrar',
    'class' => 'btn btn-primary'
]);
```

Este código generará un formulario con un campo de texto, un textarea, un checkbox y un botón de envío.

### Seguridad

Los métodos proporcionados por la clase `Form` permiten generar formularios de manera segura y dinámica, asegurando que los valores y etiquetas de los campos estén correctamente configurados para evitar errores en la validación y procesamiento del formulario.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.