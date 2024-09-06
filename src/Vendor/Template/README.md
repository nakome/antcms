# AntCMS - Template

**AntCMS** incluye una clase llamada `Template` que gestiona el renderizado de plantillas y el almacenamiento en caché de vistas generadas, proporcionando una forma eficiente de manejar la representación de contenido dinámico en HTML.

## Requisitos

- **PHP** 7.4 o superior.

## Instalación

Asegúrate de que el archivo `Template.php` se encuentra en la ruta correcta dentro de tu proyecto, bajo `src/Vendor/Template`.

```bash
src/
└───Vendor/
    └───Template/
        └───Template.php
```

## Uso de la clase Template

### Ejemplo básico de uso

#### 1. Dibujar una plantilla HTML

La clase `Template` permite cargar y dibujar archivos de plantillas. Usa el método `draw` para procesar una plantilla y obtener el contenido renderizado.

```php
<?php

use Vendor\Template\Template;

// Instanciar la clase Template
$template = new Template();

// Renderizar una plantilla
$html = $template->draw('ruta/a/plantilla.html');

echo $html;
```

Este ejemplo carga la plantilla `plantilla.html` y la renderiza, generando el contenido HTML resultante.

#### 2. Cachear plantillas

La clase `Template` automáticamente gestiona el almacenamiento en caché de plantillas HTML generadas. Esto mejora el rendimiento al evitar renderizados innecesarios. La caché se elimina automáticamente después de un día, gracias al método `removeCacheOneDay`.

```php
<?php

use Vendor\Template\Template;

// Instanciar la clase Template
$template = new Template();

// Eliminar caché después de un día
$template->removeCacheOneDay();
```

Este método asegura que los archivos en caché se mantengan actualizados eliminándolos periódicamente.

### Métodos disponibles

#### 1. `draw(string $file): string`
Este método procesa la plantilla especificada y devuelve el HTML generado.

- **Parámetro**:
  - `$file`: La ruta del archivo de plantilla.
  
- **Devuelve**: El contenido HTML generado por la plantilla.

```php
$html = $template->draw('views/home.html');
```

#### 2. `removeCacheOneDay(): void`
Este método elimina automáticamente archivos de caché que tienen más de un día de antigüedad.

- **Sin parámetros**.
  
```php
$template->removeCacheOneDay();
```

#### 3. `comment(string $content): string`
Este método permite agregar comentarios en una plantilla sin que sean visibles en la salida HTML.

- **Parámetro**:
  - `$content`: El comentario que se desea ocultar.
  
- **Devuelve**: Un valor nulo, ya que los comentarios no se muestran.

```php
$template->comment('Este es un comentario oculto');
```

### Ejemplo avanzado

Puedes usar variables dentro de las plantillas y reemplazarlas en el contenido renderizado:

```php
<?php

use Vendor\Template\Template;

// Instanciar la clase Template
$template = new Template();

// Asignar variables al contenido
$template->tags = [
    'nombre' => 'Juan',
    'mensaje' => 'Bienvenido a AntCMS'
];

// Dibujar la plantilla con las variables reemplazadas
$html = $template->draw('ruta/a/plantilla.html');

echo $html;
```

Si la plantilla contiene referencias a `{{nombre}}` o `{{mensaje}}`, estas serán reemplazadas por los valores asignados.

### Plantillas con caché

El sistema de caché en la clase `Template` almacena las vistas generadas en la carpeta `tmp/` para evitar procesarlas de nuevo, mejorando así el rendimiento.

```php
<?php

use Vendor\Template\Template;

// Instanciar la clase Template
$template = new Template();

// Renderizar la plantilla y almacenar el resultado en caché
$html = $template->draw('ruta/a/plantilla.html');

// Eliminar archivos de caché después de un día
$template->removeCacheOneDay();
```

### Seguridad

La clase `Template` trabaja en conjunto con la clase `Sanitize` para asegurarse de que el contenido HTML esté limpio de etiquetas y scripts no deseados. Esto protege las plantillas de posibles inyecciones de código.

## Licencia

Este proyecto está licenciado bajo la **MIT License**. Consulta el archivo [LICENSE](LICENSE) para más detalles.