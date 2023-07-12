# AntCMS

## Que es AntCMS

AntCMS es un simple gestor de contenidos en formato **Flat File** que a diferencia de otros que usan **Markdown** este usa **Html** normal, lo que hace que sea más rápido a la hora de cargar las páginas. Dentro de la carpeta **public** se encontrará todo lo necesario para crear la web como son:

- **Blocks** trozos de código que se usan para no tener que repetir contenido.
- **Content** Las paginas de la web.
- **Icons** Iconos que se van a usar (Opcional).
- **Images** Imagenes que se van a usar (Opcional)
- **Javascript** Archivos Js para la web.
- **Stylesheet** Archivos Css para la web.
- **Views** Plantilla de la web.

### Instalación

Copie el contenido en su hosting y abra el archivo ```app/config/default.php``` y escriba la **url** y demas datos de su web.

AntCMS támbien funciona solo con **Php** usando en la carpeta raiz: 

    php -S localhost:3000

> Recuerde añadir la url en el archivo de configuración.


Si necesita añadir mas opciones en la plantilla puede hacerlo modificando la clase **Functions** ubicada en ```app/Functions``` o añadiendo una nueva variable en el archivo de ```app/config/templating.php```.


## Estructura de archivos

Siempre que sea posible, evite modificar los archivos principales de **AntCMS**.

    website
        ├─ app
        │  ├─ AntCMS.php
        │  ├─ Config.php
        │  ├─ Functions.php/ // funciones de la plantilla
        │  └─ Templating.php
        ├─ public
        │  ├─ blocks/
        │  │  └─ info.html // bloques de texto 
        │  ├─ content/
        │  │  ├─ 404.html
        │  │  ├─ blog
        │  │  │  └─ index.html
        │  │  └─ index.html
        │  ├─ javasacript/
        │  ├─ icons/
        │  ├─ images/
        │  ├─ stylesheets/
        │  ├─ views/
        │  │  ├─ partials
        │  │  │  ├─ head.html
        │  │  │  ├─ footer.html
        │  │  │  └─ etc..
        │  │  ├─ 404.html
        │  │  ├─ group.html
        │  │  └─ index.html
        ├─ index.php
        ├─ robots.txt
        ├─ humans.txt
        └─ tmp

Las Acciones son funciones que podemos integrar en la plantilla para hacerla mas dináminca. Tenemos unas cuantas por defecto que son:

- head: usada para incluir los estilos.
- theme_before: comentarios tipo discus
- theme_after: resolución de formularios
- footer: Analytics y javascript

### Blocks

Se trata de un simple archivo html situado en la carpeta ```/blocks``` que podemos usar la etiqueta ```{Blocks: nombre-del-archivo}```.

Es práctico para usar trozos de código reusables en la plantilla.

### Estructura de la página.

La página usa serie de variables predefinidas para sacarle mas partido a la plantilla y hacerla mas dinámica tener una página completamente diferente una de otra solo modificando un archivo.

    Title:
    Description:
    Tags:
    Author:
    Image: // href file
    Date:
    Robots:
    Keywords:
    Category:
    Template: // index,post
    Published: // true, false
    Background: // blue, #f55,rgb(0,0,0)
    Video: // src file
    Color: // blue, #f55,rgb(0,0,0)
    Css: // src file
    Javascript: // src file
    Attrs: // = [1,2,true,'string']
    Json: // = json file
    ----

    <div style="background-color:{$page.background}">
        <h1>{$page.title}<h1>
        <h2>{$page.description}<h2>
        {$page.content}
    </div>

 

**Plantilla básica**. index.html

    {* head (metatags, css, etc..)  *}
    {Partial: inc/head.inc.html}
    {* action (accion que cargue antes del contenido)*}
    {Action: theme_before}
    {* contenido *}
    {$page.content}
    {* action (accion que cargue despues del contenido)*}
    {Action: theme_after}
    {* footer (scripts, etc..) *}
    {Partial: inc/footer.inc.html}

**Glosario variables de plantilla**.

| Código	| Información |
| --------- | ----------- |
| {* comentario *}	| Simple comentario. |
| {date}	| Obtener fecha. |
| {Year}	| Obtener año. |
| {Site_url}	| Obtener url del sitio. |
| {Site_current}	| Obtener hash del sitio. |
| {Pages: nombre}	| Obtener listado paginas. |
| {If:}	| Inicio condicional. |
| {Else}	| Condicional else. |
| {Elseif:}	| Condicional else if. |
| {/If}	| Cierre condicional. |
| {Segment:}	| Inicio segmento, (Condicional para segmento de url). |
| {/Segment}	| Fin condicional. |
| {Loop: $datos as $key=>$val}	| Bucle foreach ( $datos as $key=>$val). |
| {Loop: $datos as $dato}	| Bucle foreach ( $datos as $dato). |
| {/Loop}	| Fin loop. |
| {? $var = 'nueva variable' ?}	| Crear una nueva variable. |
| {?= $var?} o {$var}	| Llamar nueva variable. |
| {$page.title}	| Obtenemos las variables de la página (title,description, etc..). |
| {$config.title}	| Obtenemos las variables del archivo config (title,description, etc..). |
| {'nombre'|capitalize}	| Capitalizar texto. |
| {'nombre'|lower}	| Descapitalizar. |
| {Action: nombre}	| Llamar acciones. |
| {Include: archivo}	| Incluir archivo. |
| {Block: archivo}	| Incluir bloque. |
| {Partial: archivo}	| Incluir trozo de texto dentro de el directorio la plantilla. |
| {Assets: archivo}	| Incluir archivo de la carpeta de assets de la plantilla. |
| {Iframe: archivo}	| Incluir iframe. |
| {Youtube: archivo}	| Incluir id de video de youtube (Opción de formato 1x1,16x9,21x9). |
| {Vimeo: archivo}	| Incluir id de video de vimeo (Opción de formato 1x1,16x9,21x9). |

> Nota: puede incluir su propia plantilla en ```antcms/config/templating.php```