
# AntCMS

**AntCMS** es un CMS de archivos planos (Flat File CMS) que no requiere base de datos, lo que lo hace rápido, ligero y fácil de usar. Este proyecto está diseñado para proporcionar una forma simple y segura de gestionar contenido estático, ahora con características adicionales como un administrador de archivos y mejoras de seguridad.


## Requisitos

- **PHP** 7.4 o superior.
- Servidor web como **Apache** o **Nginx**.
- Extensión **mbstring** habilitada (u otras extensiones requeridas por el proyecto).

## Instalación

1. Clona el repositorio:

   ```bash
   git clone https://github.com/tu-usuario/AntCMS.git


## Estructura del Proyecto

La estructura de **AntCMS** está diseñada para ser modular y fácil de navegar. A continuación, se presenta un resumen de los directorios más importantes:

```bash
├───config             # Configuraciones del sistema
├───panel              # Archivos del panel de administración
├───public             # Recursos públicos accesibles desde la web
│   ├───blocks         # Bloques de contenido reutilizable
│   ├───content        # Contenido del sitio, como páginas y secciones
│   │   ├───blog       # Sección del blog
│   │   └───documentacion  # Documentación del CMS
│   ├───documents      # Archivos y componentes web utilizados
│   ├───fonts          # Fuentes tipográficas utilizadas en el CMS
│   ├───icons          # Iconos utilizados en el CMS
│   ├───images         # Imágenes del sitio
│   ├───javascript     # Archivos JavaScript personalizados
│   ├───stylesheets    # Archivos CSS personalizados
│   └───views          # Vistas del frontend
│       └───partials   # Plantillas parciales (e.g., meta tags)
├───src                # Código fuente principal
│   ├───Ant            # Módulos de back-end y front-end
│   │   ├───BackEnd    # Lógica del lado del servidor
│   │   │   └───Traits # Traits reutilizables para el backend
│   │   └───FrontEnd   # Lógica del lado del cliente
│   │       └───Traits # Traits reutilizables para el frontend
│   └───Vendor         # Dependencias y librerías de terceros
├───tmp                # Archivos temporales generados por el sistema
```

### Descripción de Carpetas Clave

- **config**: Archivos de configuración del sistema.
- **panel**: Archivos relacionados con el panel de administración del CMS.
- **public**: Contiene todos los recursos que serán accesibles públicamente, como imágenes, hojas de estilo, y scripts.
- **src/Ant**: El núcleo del sistema CMS, dividido en las partes del frontend y backend.
- **src/Vendor**: Librerías de terceros utilizadas por el CMS (dependencias).
- **tmp**: Archivos temporales generados por el sistema durante su ejecución.

Esta estructura modular facilita la escalabilidad y mantenimiento del CMS, permitiendo una clara separación entre la lógica de negocio, los recursos del front-end y los archivos temporales.

Aquí tienes una explicación detallada del archivo `defines.php` que puedes incluir en tu `README.md` o documentación.


### Explicación del archivo `defines.php`

El archivo `defines.php` es un archivo clave en **AntCMS** que define una serie de constantes globales utilizadas para la configuración del sistema, manejo de seguridad, y otras configuraciones importantes del CMS. A continuación, una descripción de las secciones principales:

1. **Configuraciones de idioma y codificación:**
   - `HTML_LANG`, `LANG`, `LANG_FALLBACK_1`, `LANG_FALLBACK_2`: Define el idioma principal y de reserva del CMS.
   - `CHARSET`: Establece la codificación de caracteres (UTF-8).

2. **Zona horaria y versión mínima de PHP:**
   - `TIMEZONE`: Define la zona horaria usada en la aplicación.
   - `ROOT_MINIMUM_PHP`: Verifica si la versión de PHP es al menos 7.4.

3. **Seguridad:**
   - `NONCE`: Genera un nonce para reforzar las políticas de seguridad de contenido (CSP).
   - Define cabeceras de seguridad como `Strict-Transport-Security`, `Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options`, entre otras.

4. **Configuración de sesión:**
   - Ajustes como `SESSION_USE_ONLY_COOKIES` y `SESSION_COOKIE_HTTPONLY` protegen la sesión al usar solo cookies y evitar que se transmitan cookies por otros medios inseguros.

5. **Debug y logging:**
   - `DEBUG`: Si está habilitado, muestra errores; si no, oculta los errores.
   - `ERROR_LOG_FILE`: Define la ruta del archivo de log de errores.

6. **URLs y rutas del sistema:**
   - `SITE_URL` y `ADMIN_URL`: Centralizan la URL base del sitio y el panel de administración.
   - `ROOT` y `PUBLIC_ROOT`: Define las rutas del sistema y la pública.

   En el archivo `defines.php` que compartiste, hay varias mejoras de seguridad definidas que refuerzan la protección del sistema **AntCMS**. A continuación te explico en detalle cada una de estas mejoras para que puedas añadirlas a tu documentación o `README.md`.


### Archivo de configuración

*   `'url'`: La URL base de tu sitio web.
*   `'google-site-verification'`: Código de verificación de Google para la integración con Google Search Console.
*   `'theme_color'`: Color principal del tema de tu sitio.
*   `'background_color'`: Color de fondo de tu sitio.
*   `'orientation'`: Orientación predeterminada de tu sitio.
*   `'display'`: Modo de visualización predeterminado de tu sitio.
*   `'short_name'`: Nombre corto de tu sitio.
*   `'lang'`: Idioma predeterminado de tu sitio.
*   `'charset'`: Conjunto de caracteres predeterminado para tu sitio.
*   `'timezone'`: Zona horaria predeterminada para tu sitio.
*   `'title'`: Título predeterminado de tu sitio.
*   `'description'`: Descripción predeterminada de tu sitio.
*   `'keywords'`: Palabras clave predeterminadas para tu sitio.
*   `'author'`: Autor predeterminado de tu sitio.
*   `'email'`: Correo electrónico de contacto predeterminado para tu sitio.
*   `'default_image'`: Ruta de la imagen predeterminada para tu sitio.
*   `'pagination'`: Número predeterminado de elementos por página para la paginación.
*   `'copyright'`: Derechos de autor predeterminados.
*   `'menu'`: Menú de navegación de tu sitio, donde la clave es la URL y el valor es el nombre del enlace.
*   `'notPublished'`: Configuración para la página que aún no ha sido publicada o está desactivada, incluyendo título, descripción y contenido.

Estas configuraciones te permiten personalizar diversos aspectos de tu sitio web en AntCMS.

### Creación de una Página

El gestor de contenidos permite la creación de páginas mediante la definición de un archivo HTML con una serie de variables y el contenido deseado. El formato del archivo es el siguiente:

```bash
Title: Flat file CMS?
Description: Un sistema de contenido de archivos sin bases de datos.
Published: trueDate: 02/10/2021
Template: index
----
contenido de la pagina
```

#### Descripción de las Variables:

*   **Title:** Define el título de la página.
*   **Description:** Describe brevemente el contenido de la página.
*   **Tags:** Etiquetas asociadas a la página.
*   **Author:** Nombre del autor de la página.
*   **Image:** Enlace al archivo de imagen asociado a la página.
*   **Date:** Fecha de creación o última modificación de la página en formato DD-MM-YY.
*   **Robots:** Especifica la directiva para la indexación de los motores de búsqueda.
*   **Keywords:** Palabras clave asociadas a la página.
*   **Category:** Categoría a la que pertenece la página.
*   **Template:** Plantilla a utilizar para la visualización de la página, ya sea para la vista de índice o de entrada.
*   **Published:** Indica si la página está publicada (true) o no (false).
*   **Background:** Define el color de fondo de la página.
*   **Video:** Enlace al archivo de video asociado a la página.
*   **Color:** Define el color principal de la página.
*   **Css:** Enlace al archivo CSS asociado a la página.
*   **Javascript:** Enlace al archivo JavaScript asociado a la página.
*   **Attrs:** Atributos adicionales para la página, especificados en formato de lista.
*   **Json:** Enlace al archivo JSON asociado a la página.

Una vez definidas las variables, el contenido de la página se coloca después de la línea de separación (`----`).

Por supuesto, podemos agregar el glosario de variables que se utiliza en la plantilla. Aquí está el contenido para ello:

### Glosario de Variables en la Plantilla

La plantilla del gestor de contenidos utiliza una serie de variables especiales para facilitar la manipulación y presentación de datos. A continuación se presenta un glosario de las variables disponibles:

### Descripción de Variables:

*   **{\* comentario \*}:** Permite incluir comentarios en el código de la plantilla.
*   **{date}:** Recupera la fecha actual.
*   **{Year}:** Recupera el año actual.
*   **{Site\_url}:** Obtiene la URL del sitio.
*   **{Site\_current}:** Obtiene el hash del sitio.
*   **{Pages: nombre}:** Obtiene un listado de páginas.
*   **{If:}:** Inicia una condición.
*   **{Else}:** Bloque que se ejecuta si la condición no se cumple.
*   **{Elseif:}:** Bloque que se ejecuta si otra condición se cumple.
*   **{/If}:** Cierra una condición.
*   **{Segment:}:** Inicia un segmento condicional para la URL.
*   **{/Segment}:** Cierra un segmento condicional.
*   **{Loop: $datos as $key=>$val}:** Inicia un bucle foreach.
*   **{/Loop}:** Cierra un bucle foreach.
*   **{? $var = 'nueva variable' ?}:** Crea una nueva variable.
*   **{?= $var?} o {$var}:** Llama a una variable existente.
*   **{$page.title}:** Obtiene variables específicas de la página, como el título y la descripción.
*   **{$config.title}:** Obtiene variables del archivo de configuración, como el título y la descripción etc...
*   **{$page.title|capitalize}:** Esta variable toma el título de la página y lo convierte en mayúscula solo para la primera letra.
*   **{$page.title|lower}:** Esta variable toma el título de la página y lo convierte completamente en minúsculas.
*   **{$page.title|upper}:** Esta variable toma el título de la página y lo convierte completamente en mayúsculas.
*   **{$page.title|truncate:5}:** Esta variable toma el título de la página y lo recorta a una longitud máxima de 5 caracteres.
*   **{Action: nombre\_accion}:** Esta acción ejecuta una acción específica definida en la plantilla o en el código de AntCMS.
*   **{Block: nombre\_bloque}:** Esta acción incluye un bloque específico de contenido en la plantilla. El bloque debe estar definido en algún lugar de la plantilla o en un archivo relacionado.
*   **{Partial: parte\_archivo\_view}:** Esta acción incluye una parte específica de un archivo de vista en la plantilla.
*   **{Assets: url\_carpeta\_publica}:** Esta acción incluye un archivo o recurso ubicado en una carpeta pública de tu aplicación.

Este glosario proporciona una referencia rápida para utilizar las variables en la plantilla del gestor de contenidos.

### Creación de Nuevas Variables de Plantilla

El gestor de contenidos permite la creación de nuevas variables de plantilla para personalizar aún más la presentación y funcionalidades de las páginas. Para crear una nueva variable de plantilla, sigue estos pasos:

1.  **Definir la nueva variable:** Decide el nombre y la funcionalidad de la nueva variable que deseas crear.
    
2.  **Agregar la variable al arreglo `$templating`:** Abre el archivo que contiene el arreglo `$templating` y agrega una nueva entrada con el siguiente formato:
    
    ```php
    $templating = ['{Nueva_variable}' => '<?php // Código PHP que define el comportamiento de la nueva variable ?>',// Otras variables existentes...];
    ```
    
    Reemplaza `'Nueva_variable'` con el nombre de tu nueva variable y proporciona el código PHP necesario para que la variable funcione según tus requisitos.
    
3.  **Utilizar la nueva variable en la plantilla:** Una vez que hayas definido la nueva variable en el arreglo `$templating`, puedes utilizarla en tu plantilla HTML de la siguiente manera:
    
    ```html
    <p>La nueva variable es: {Nueva_variable}</p>
    ```
    
    Asegúrate de reemplazar `'Nueva_variable'` con el nombre de la variable que definiste en el paso anterior.
    

Siguiendo estos pasos, podrás crear y utilizar nuevas variables de plantilla según tus necesidades específicas, lo que te permitirá personalizar aún más la apariencia y funcionalidad de tu sitio web.

_Puedes ver un ejemplo en la variable `$templating` en el archivo `index.php`._

### Plantillas en AntCMS

La estructura de las plantillas en AntCMS se organiza en la carpeta "views", que contiene varios archivos HTML que representan las diferentes páginas de tu sitio web. Aquí hay una descripción de la estructura y el contenido básico de la carpeta "views":

```bash
views
├─ partials
│  ├─ footer.inc.html
│  ├─ head.inc.html
├─ 404.html
├─ group.html
└─ index.html
```

En la carpeta "views", tenemos subcarpetas como "partials", que contiene fragmentos de HTML reutilizables que se incluyen en múltiples plantillas.

La plantilla básica se encuentra en "index.html" y sigue esta estructura:

```html
{* head (metatags, css, etc..)  *}
{Partial: inc/head.inc.html}
{* action (acción que se ejecuta antes del contenido) *}
{Action: theme_before}
{* contenido *}
{$page.content}
{* action (acción que se ejecuta después del contenido) *}
{Action: theme_after}
{* footer (scripts, etc..) *}
{Partial: inc/footer.inc.html}
```

En esta plantilla básica:

*   Se incluye el fragmento "head.inc.html" que contiene metatags, enlaces a CSS y cualquier otro contenido relacionado con el encabezado de la página.
*   Se ejecuta una acción antes de mostrar el contenido principal de la página, lo que permite realizar operaciones o cargar datos adicionales.
*   Se muestra el contenido principal de la página, representado por la variable `{$page.content}`.
*   Se ejecuta una acción después de mostrar el contenido principal, lo que permite realizar operaciones adicionales o cargar elementos adicionales.
*   Se incluye el fragmento "footer.inc.html" que contiene scripts u otros elementos relacionados con el pie de página.

Aquí está la documentación sobre la creación de acciones en AntCMS:

### Acciones

Las acciones son funciones que podemos integrar en la plantilla para hacerla más dinámica. Algunas acciones por defecto son:

*   **head**: Usada para incluir los estilos.
*   **theme\_before**: Comentarios tipo discusión.
*   **theme\_after**: Resolución de formularios.
*   **footer**: Analytics y JavaScript.

#### Creando Acciones

Vamos a crear una acción que automáticamente genere un enlace al final de cada página usando una acción que ya está en la plantilla, que es `Ant\FrontEnd\FrontEnd::runAction('theme_after');`.

```php
<?php
   Ant\FrontEnd\FrontEnd::actionAdd('theme_after', function(){  
     echo '<a href="'.Ant\FrontEnd\FrontEnd::urlBase().'/articulos">Ver artículos.</a>';
   });
?>
```

Y ahora, en todas las páginas al final se verá ese enlace, así de fácil.

Ahora vamos a añadir algo más. Le diremos que si está en la sección de artículos y la página es "extensiones", enseñe el texto, y si no, no enseñe nada.

```php
<?php
   Ant\FrontEnd\FrontEnd::actionAdd('theme_after', function(){ 
    if(Ant\FrontEnd\FrontEnd::urlSegment(0) == 'articulos' && Ant\FrontEnd\FrontEnd::urlSegment(1) == 'extensiones'){  
     echo '<a href="'.Ant\FrontEnd\FrontEnd::urlBase().'/articulos">Ver artículos.</a>'; 
    }
   });?>
```

Ahora haremos una acción que cambie el fondo solo en esta página, para ello usaremos el `Ant\FrontEnd\FrontEnd::actionRun('head')` que hay en el archivo "head.inc.html".

```php
<?php
   Ant\FrontEnd\FrontEnd::actionAdd('head', function(){ 
     if(Ant\FrontEnd\FrontEnd::urlSegment(0) == 'articulos' && Ant\FrontEnd\FrontEnd::urlSegment(1) == 'extensiones'){
    echo '<style rel="stylesheet">
      body{
        background:blue;
        color:white;
      }
      pre, code{
        background: #0000bb;
        border-color: #00008e;
        box-shadow: 0px 3px 6px -2px #02026f;
        color: white;
      }
     </style>'; 
    }
});?>
```

Con estas acciones, podrás personalizar fácilmente el comportamiento y la apariencia de tus páginas en AntCMS.


### Mejoras de seguridad

El CMS **AntCMS** incorpora varias configuraciones de seguridad para proteger tanto el sistema como los usuarios. Estas medidas se encuentran principalmente en las cabeceras HTTP y en la configuración del sistema. A continuación, se describen las principales:

#### 1. **Content Security Policy (CSP)**

```php
define('CONTENT_SECURITY_POLICY', "img-src 'self' data:; script-src 'self' https://cdn.jsdelivr.net 'nonce-%s'");
```

- **Descripción**: La política de seguridad de contenido (CSP) ayuda a prevenir ataques como **Cross-Site Scripting (XSS)** al controlar qué fuentes de contenido pueden cargarse en el sitio.
- **Detalle**: La CSP especificada permite solo cargar imágenes desde el propio servidor (`'self'`) o de datos inline (`data:`). Los scripts solo se pueden cargar desde el propio servidor o desde un CDN de confianza (`https://cdn.jsdelivr.net`), usando un `nonce` único para proteger los scripts dinámicos.
- **Beneficio**: Ayuda a mitigar la inyección de scripts maliciosos desde fuentes no confiables.

#### 2. **Strict Transport Security (HSTS)**

```php
define('STRICT_TRANSPORT_SECURITY', 'max-age=31536000');
```

- **Descripción**: La cabecera HSTS (HTTP Strict Transport Security) asegura que el navegador siempre se comunique con el servidor usando HTTPS.
- **Detalle**: La configuración `max-age=31536000` obliga al navegador a utilizar HTTPS durante un año completo (31,536,000 segundos).
- **Beneficio**: Previene ataques de **downgrade** y **Man-in-the-Middle (MITM)** al asegurar que las conexiones sean siempre cifradas.

#### 3. **X-Frame-Options**

```php
define('X_FRAME_OPTIONS', 'SAMEORIGIN');
```

- **Descripción**: Esta cabecera impide que el contenido del sitio sea embebido en un `iframe` desde otros dominios.
- **Detalle**: Con el valor `SAMEORIGIN`, solo se permite que el contenido se embeba desde el mismo dominio.
- **Beneficio**: Ayuda a prevenir ataques de **clickjacking**.

#### 4. **X-Content-Type-Options**

```php
define('X_CONTENT_TYPE_OPTIONS', 'nosniff');
```

- **Descripción**: Evita que los navegadores intenten adivinar el tipo de contenido de los archivos que se reciben.
- **Beneficio**: Previene que un atacante pueda inyectar contenido malicioso de tipo MIME diferente al esperado, protegiendo contra ataques de tipo **MIME sniffing**.

#### 5. **Referrer Policy**

```php
define('REFERRER_POLICY', 'no-referrer-when-downgrade');
```

- **Descripción**: Controla cuándo y cómo se envía la información de referencia (el origen de la solicitud) al navegar entre páginas.
- **Detalle**: La política `no-referrer-when-downgrade` no enviará información de referencia cuando se navegue de un sitio HTTPS a uno HTTP.
- **Beneficio**: Protege la privacidad de los usuarios y reduce la filtración de información a sitios menos seguros.

#### 6. **Permissions Policy**

```php
define('PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=()');
```

- **Descripción**: Esta cabecera define qué APIs del navegador están habilitadas o deshabilitadas en la página.
- **Detalle**: En este caso, se deshabilitan los permisos para **geolocalización**, **micrófono**, y **cámara**.
- **Beneficio**: Restringe el acceso a características sensibles del dispositivo, protegiendo la privacidad del usuario.

#### 7. **Configuración de sesión segura**

```php
define('SESSION_USE_ONLY_COOKIES', 1);
define('SESSION_USE_TRANS_SID', 0);
define('SESSION_COOKIE_HTTPONLY', 1);
```

- **Descripción**: Estas configuraciones aseguran el manejo seguro de las sesiones de usuario.
  - **SESSION_USE_ONLY_COOKIES**: Solo permite que las sesiones se almacenen en cookies, no en URLs, para evitar la exposición de sesiones en enlaces compartidos.
  - **SESSION_USE_TRANS_SID**: Desactiva el uso de IDs de sesión en la URL, reduciendo el riesgo de que un atacante robe una sesión por medio de URL-sharing.
  - **SESSION_COOKIE_HTTPONLY**: Previene el acceso a las cookies de sesión mediante JavaScript, mitigando ataques de **XSS**.
- **Beneficio**: Mejora la seguridad de las sesiones al reducir los vectores de ataque como el robo de cookies o IDs de sesión.

#### 8. **Nonce para scripts**

```php
define('NONCE', base64_encode(random_bytes(16)));
```

- **Descripción**: El `nonce` es un valor único generado en cada solicitud y usado para validar los scripts permitidos en la página.
- **Detalle**: Este `nonce` es insertado en la política de seguridad de contenido (CSP) para asegurarse de que solo los scripts autorizados por el servidor puedan ejecutarse.
- **Beneficio**: Evita que scripts maliciosos sean ejecutados en el navegador, proporcionando una capa adicional de protección contra **XSS**.

#### 9. **Archivo de log de errores**

```php
define('ERROR_LOG_FILE', PUBLIC_ROOT . '/tmp/php-error.log');
```

- **Descripción**: Define una ruta específica para almacenar los errores de PHP en un archivo de log.
- **Beneficio**: Centraliza los errores del sistema en un archivo seguro, lo que facilita el monitoreo y depuración sin exponer información sensible al usuario final.

### Resumen de Beneficios

Las mejoras de seguridad implementadas en **AntCMS** tienen los siguientes objetivos clave:

- Proteger contra ataques comunes como **XSS**, **clickjacking**, **MIME sniffing**, y **MITM**.
- Asegurar la privacidad del usuario al limitar el envío de información sensible.
- Refuerzan la seguridad del manejo de sesiones y cookies para evitar el robo de sesiones.
- Mejoran el manejo de errores sin exponer información innecesaria al público.

Estas configuraciones de seguridad están pensadas para hacer de **AntCMS** un sistema más seguro y robusto frente a los ataques más comunes en aplicaciones web.

Puedes añadir estas explicaciones a tu `README.md` o a una sección de seguridad en tu documentación para que otros desarrolladores y usuarios puedan comprender cómo está protegido el sistema.