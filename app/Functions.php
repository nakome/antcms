<?php

declare (strict_types = 1);

defined('SECURE') or die('No tiene acceso al script.');

/**
 * Este código define una serie de "traits" o rasgos que describen comportamientos o características de clases.
 * 1º "ArticlesTrait" define comportamientos relacionados con artículos.
 * 2º "ContactTrait" define comportamientos relacionados con contactos.
 * 3º "MenuTrait" define comportamientos relacionados con menús.
 * 4º "NavFolderTrait" define comportamientos relacionados con carpetas de navegación.
 * 5º "SearchTrait" define comportamientos relacionados con la búsqueda.
 * 6º "Functions" hereda los comportamientos definidos en los traits.
 */

/**
 * Este código es un fragmento de PHP que contiene varias funciones definidas dentro de un Trait llamado
 * "ArticlesTrait". Las funciones incluidas son las siguientes:
 * __emptyPage(): Esta función devuelve el valor del parámetro "page" enviado a través de $_GET o "0" si no
 * existe.
 * __listItem(): Esta función genera una lista de elementos HTML, incluyendo un enlace y un texto. Los
 * parámetros son la clase CSS del elemento de lista, la clase CSS del enlace, el número de página y el texto
 * del enlace.
 * __tplPag(): Esta función genera la paginación de los artículos en la página. Los parámetros son el array de
 * artículos, el límite de artículos por página y el número máximo de artículos que se mostrarán. Esta función
 * calcula el número total de páginas necesarias, establece la página actual y la página anterior y siguiente,
 * y muestra la paginación en forma de lista HTML.
 * __tplPosts(): Esta función genera el HTML para mostrar los artículos en la página. Los parámetros son el
 * array de artículos. Esta función recorre cada artículo y genera el HTML para cada uno de ellos, incluyendo
 * el título, la descripción, la fecha, los tags y un enlace al artículo completo.
 */
trait ArticlesTrait
{

    /**
     * Comprobar vacio
     *
     * @return ?string
     */
    private function __emptyPage(): string
    {
        return $_GET['page'] ?? '0';
    }

    /**
     * Lista li
     *
     * @param string $liclass
     * @param string $linkclass
     * @param int|float $pag
     * @param string|int $txt
     *
     * @return string
     */
    private function __listItem(
        string $liclass,
        string $linkclass,
        ? float $pag,
        mixed $txt
    ) : string {
        return <<<HTML
            <li class="{$liclass}">
                <a class="{$linkclass}" href="?page={$pag}">
                    {$txt}
                </a>
            </li>
        HTML;
    }

    /**
     * Renderizar paginación
     *
     * @param array $posts
     * @param int $limit
     * @param int $num
     *
     * @return void
     */
    private function __tplPag(array $posts, int $limit, int $num): void
    {
        // Calcular el número total de páginas necesarias
        $totalPages = ceil(count($posts) / $limit);
        // Establecer la página actual a mostrar
        $currentPage = $this->__emptyPage();
        // Establecer la página anterior y siguiente
        $prevPage = $currentPage - 1;
        $nextPage = $currentPage + 1;

        // Si el número de publicaciones es menor o igual que el número de publicaciones especificado, no hay necesidad de mostrar paginación
        if (count($posts) <= $num) {
            return;
        }

        // Iniciar la lista de paginación
        echo '<nav aria-label="Paginacion"><ul class="pagination">';

        // Mostrar la página anterior
        echo $this->__listItem("page-item " . ($prevPage < 0 ? "disabled" : ""), "page-link", $prevPage, $this->leftArrow());

        // Mostrar el botón "Primera" si no estamos en la primera página
        if ($currentPage > 0) {
            echo $this->__listItem("page-item", "page-link", 0, "Primera");
        }

        // Calcular qué páginas mostrar en la lista
        $startPage = max(1, $currentPage - 5);
        $endPage = min($currentPage + 6, $totalPages);

        // Mostrar cada página en la lista
        for ($i = $startPage; $i < $endPage; $i++) {
            // Determinar si esta página es la actual
            $isActive = $i === $currentPage;
            // Establecer la clase de la página
            $class = "page-item" . ($isActive ? " active" : "");
            // Mostrar la página
            echo $this->__listItem($class, "page-link", $i, $i);
        }

        // Mostrar el botón "Última" si no estamos en la última página
        if ($currentPage < $totalPages - 1) {
            echo $this->__listItem("page-item", "page-link", $totalPages - 1, "Ultima");
        }

        // Mostrar la página siguiente
        echo $this->__listItem("page-item " . ($nextPage >= $totalPages ? "disabled" : ""), "page-link", $nextPage, $this->rightArrow());
        // Cerrar la lista de paginación
        echo '</ul></nav>';
    }

    /**
     * Renderizar paginación
     *
     * @param array $items
     *
     * @return void
     */
    private function __tplPosts(array $items): void
    {
        // Inicializar variable vacía para concatenar los artículos
        $html = '';

        // Recorrer cada artículo y construir su HTML
        foreach ($items as $articulo) {

            // Si el artículo no está publicado, pasar al siguiente
            if (!in_array($articulo['published'], ['true', true, 1])) {
                continue;
            }

            // Obtener la URL base del sitio
            $site_url = AntCMS::urlBase();

            // Convertir la lista de tags en HTML
            $tags = explode(',', $articulo['tags']);
            $tags_html = '';
            foreach ($tags as $tag) {
                $tags_html .= "<a class=\"text-decoration-none me-1\" href=\"{$site_url}/blog?buscar={$tag}\">{$tag}</a>";
            }

            // Formatear la fecha
            $date = date('d-m-Y', $articulo['date']);

            // Truncar el texto del artículo a 80 caracteres
            $txtShort = AntCMS::short($articulo['description'], 80);

            // Concatenar el HTML del artículo a la variable $html
            $html .= <<<HTML
            <article class="my-3 py-3">
                <a href="{$articulo['url']}">
                    <h2 class="fs-3 fw-normal text-primary mb-2 lh-base">{$articulo['title']}</h2>
                    <h3 class="fs-5 fw-lighter text-muted mb-2 lh-sm">{$txtShort}</h3>
                </a>
                <p class="post-meta">
                    <strong class="text-muted">Fecha:&nbsp;</strong><a href="{$site_url}/blog?buscar={$date}"><time datetime="{$date}">{$date}</time></a>&nbsp;
                    <strong class="text-muted">Etiquetas:&nbsp;</strong>{$tags_html}
                </p>
            </article>
            HTML;
        }

        // Imprimir el HTML generado
        echo $html;
    }

    /**
     * Articles
     *
     * @param string $name
     * @param int $num
     *
     * @return void
     */
    public static function articles(string $name, int $num): void
    {
        // Obtener las páginas con el nombre especificado, ordenadas por fecha de forma descendente
        // y excluyendo las páginas con nombre "index" y "404"
        $posts = AntCMS::run()->pages($name, 'date', 'DESC', ['index', '404']);

        // Establecer el límite de artículos por página
        $limit = $num;

        // Obtener el número de página actual a través del parámetro "page" en la URL
        $pgkey = $_GET['page'] ?? 0;

        // Si se encontraron páginas
        if (is_array($posts) && count($posts) > 0) {
            // Dividir las páginas en bloques de acuerdo al límite de artículos por página
            $files = array_chunk($posts, $limit);

            // Mostrar los artículos del bloque correspondiente a la página actual
            self::run()->__tplPosts($files[$pgkey]);

            // Mostrar la paginación
            self::run()->__tplPag($posts, $limit, $num);
        }
    }
}

/**
 * Este es un código PHP que contiene un Trait llamado "ContactTrait". El Trait tiene dos métodos públicos:
 * "captcha" y "contact".
 * El método "captcha" genera un captcha aleatorio que consta de una cadena de texto alfanumérico y devuelve
 * la cadena generada.
 * El método "contact" muestra un formulario de contacto con campos para ingresar el nombre, correo
 * electrónico, teléfono, asunto y mensaje.
 * También muestra un campo de captcha generado aleatoriamente y una etiqueta que indica al usuario que
 * escriba el valor del captcha para validar el mensaje.
 * Cuando se envía el formulario, se valida el captcha y se comprueba si el valor ingresado coincide con el
 * valor del captcha generado aleatoriamente.
 * Si el captcha es válido, el mensaje se envía por correo electrónico.
 * Si el correo electrónico se envía correctamente, se muestra un mensaje de éxito y se redirige al usuario a
 * la página de inicio después de 3 segundos.
 * Si el correo electrónico no se envía correctamente, se muestra un mensaje de error. Si el captcha no es
 * válido, se muestra un mensaje de error y el mensaje no se envía.
 * El código utiliza algunas variables y funciones definidas en otras partes del programa, como $recepient,
 * $sitename, $siteurl y algunas funciones estáticas como "inputForm" y "inputHiddenForm".
 */
trait ContactTrait
{
    /**
     * Random captcha
     *
     * @param string $input
     * @param int ·$strlen
     *
     * @return string
     */
    public static function captcha(string $input = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890', int $strlen = 6): string
    {
        $result = '';
        for ($i = 0; $i < $strlen; ++$i) {
            $result .= $input[mt_rand(0, strlen($input) - 1)];
        }
        return $result;
    }

    /**
     * Contacto
     *
     * @return void
     */
    public static function contact(): void
    {
        // mensajes para traducir
        $messages = [
            'name' => 'Nombre',
            'email' => 'Correo electronico',
            'phone' => 'Telefono',
            'subject' => 'Asunto',
            'message' => 'Mensaje',
            'captchaLabel' => 'Por favor escriba <strong>{}</strong> para validar el mensaje.',
            'errorCaptchaInfo' => '<strong>Error: </strong> el codigo de validación introducido es incorrecto.',
            'submitBtn' => 'Enviar correo',
            'pagetitle' => 'Nuevo mensaje desde la web',
            'successInfo' => 'Gracias tu mensaje ha sido enviado, volviendo al inicio en 3 segundos.',
            'errorInfo' => '<strong>Error: </strong>Lo siento hubo un problema al enviarlo por favor intentelo otra vez..',
        ];

        // numero aleatorio
        $captchaValue = self::captcha('123456789', 6);
        // email
        $recepient = AntCMS::$config['email'];
        // titulo email
        $sitename = AntCMS::$config['title'];
        // url sitio
        $siteurl = AntCMS::urlBase();

        // info click submit
        $infoOutput = '';

        // si se envia el formulario
        if (array_key_exists('enviarFormulario', $_POST)) {

            // post vars
            $subject = trim($_POST['subject']);
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = (trim($_POST['phone'])) ? trim($_POST['phone']) : 'no proporcionado';
            $text = trim($_POST['message']);
            $captcha = trim($_POST['captcha']);
            $checkCaptcha = trim($_POST['checkCaptcha']);

            // titulo del mensaje
            $pagetitle = $messages['pagetitle'];

            // mensaje de salida
            $message = "========= INFO ============\n";
            $message .= $messages['name'] . ": $name\n";
            $message .= $messages['email'] . ": $email\n";
            $message .= $messages['phone'] . ": $phone\n";
            $message .= $messages['subject'] . ": $subject\n";
            $message .= "===========================\n";
            $message .= $text;

            // comprobamos el captcha
            if ($checkCaptcha == $captcha) {
                // enviamos mail
                if (mail($recepient, $pagetitle, $message, "Content-type: text/plain; charset=\"utf-8\" \nFrom: <$email>")) {
                    // pasamos la info de que ha sido mandando
                    $infoOutput = '<div class="alert alert-info">' . $messages['successInfo'] . '</div>';
                    // volvemos al inicio
                    echo "<script rel='javascript'>setTimeout(() => {window.location.href = site_url;},3000);</script>";
                } else {
                    // error email no enviado
                    $infoOutput = '<div class="alert alert-danger">' . $messages['errorInfo'] . '</div>';
                }
            } else {
                // info error captcha
                $infoOutput = '<div class="alert alert-danger">' . $messages['errorCaptchaInfo'] . '</div>';
            }
        }

        // show error
        $html = $infoOutput;

        // iniciamos formulario html
        $html .= '<form id="contact" class="form mb-3 needs-validation" method="post">';
        // input hidden
        $html .= static::inputHiddenForm([
            'name' => 'checkCaptcha',
            'value' => $captchaValue,
        ]);
        // input name
        $html .= static::inputForm([
            'name' => 'name',
            'type' => 'text',
            'label' => $messages['name'],
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input email
        $html .= static::inputForm([
            'name' => 'email',
            'type' => 'email',
            'label' => $messages['email'],
            'pattern' => '[a-z._%+-]+@[a-z.-]+\.[a-z]{2,4}',
            'required' => (bool)true,
        ]);
        // input phone
        $html .= static::inputForm([
            'name' => 'phone',
            'type' => 'tel',
            'label' => $messages['phone'],
            'pattern' => '[0-9]{9,9}',
            'required' => (bool)true,
        ]);
        // input subject
        $html .= static::inputForm([
            'name' => 'subject',
            'type' => 'text',
            'label' => $messages['subject'],
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input message
        $html .= static::textareaForm([
            'name' => 'message',
            'label' => $messages['message'],
            'rows' => '5',
            'pattern' => '^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$',
            'required' => (bool)true,
        ]);
        // input captcha
        $html .= static::inputForm([
            'name' => 'captcha',
            'type' => 'number',
            'label' => str_replace('{}', $captchaValue, $messages['captchaLabel']),
            'required' => (bool)true,
        ]);
        // input submit
        $html .= static::submitForm([
            'name' => 'enviarFormulario',
            'value' => $messages['submitBtn'],
        ]);
        // cerramos formulario
        $html .= '</form>';
        echo $html;
    }

    /**
     * input form
     */
    public static function inputForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $label = $args['label'] ?? 'input text';
        $placeholder = $args['placeholder'] ?? '';
        $type = $args['type'] ?? 'text';
        $pattern = $args['pattern'] ?? 'name';
        $required = $args['required'] ? 'required' : '';
        $value = $args['value'] ?? '';
        return <<<HTML
            <div class="form-group mb-3">
                <label for="{$name}" class="form-label">{$label}</label>
                <input
                    class="form-control"
                    type="{$type}"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    placeholder="{$placeholder}"
                    value="{$value}"
                    {$required}
                />
            </div>
        HTML;
    }

    /**
     * input hidden form
     */
    public static function inputHiddenForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
    }

    /**
     * submit form
     */
    public static function submitForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $value = $args['value'] ?? '';
        $class = $args['class'] ?? 'btn btn-primary my-3';
        return '<input class="' . $class . '" type="submit" name="' . $name . '" value="' . $value . '" />';
    }

    /**
     * textarea form
     */
    public static function textareaForm(array $args)
    {
        $name = $args['name'] ?? 'name';
        $label = $args['label'] ?? 'message';
        $placeholder = $args['placeholder'] ?? '';
        $type = $args['type'] ?? 'text';
        $pattern = $args['pattern'] ?? '';
        $rows = $args['rows'] ?? '5';
        $required = $args['required'] ? 'required' : '';
        $value = $args['value'] ?? '';
        return <<<HTML
            <div class="form-group mb-3">
                <label for="{$name}" class="form-label">{$label}</label>
                <textarea
                    class="form-control"
                    name="{$name}"
                    id="{$name}"
                    pattern="{$pattern}"
                    rows="{$rows}"
                    placeholder="{$placeholder}"
                    {$required}
                >{$value}</textarea>
            </div>
        HTML;
    }
}

/**
 * El código es un Trait de PHP llamado "MenuTrait" que define dos métodos para generar un menú de navegación
 * a partir de un arreglo. El primer método,
 * "__createMenu", es un método privado que se encarga de generar el HTML del menú de navegación a partir de
 * un arreglo que representa la estructura del menú.
 * El segundo método, "menu", es un método público y estático que llama al método privado "__createMenu" para
 * generar el menú de navegación y lo imprime en pantalla.
 * El método "__createMenu" toma como parámetro un arreglo llamado "$nav" y devuelve una cadena de caracteres
 * que contiene el HTML del menú de navegación.
 * El método itera sobre los elementos del arreglo "$nav" y, dependiendo del tipo de elemento (si es un enlace
 * externo o un submenú), genera la salida HTML correspondiente.
 * El método "menu" obtiene el arreglo de configuración del menú desde una variable estática "$config" de la
 * clase "AntCMS" y luego llama al método privado
 * "__createMenu" para generar el HTML del menú de navegación. Finalmente, imprime el HTML generado en
 * pantalla.
 * En resumen, este Trait proporciona una forma de generar un menú de navegación en HTML a partir de un
 * arreglo de configuración en PHP.
 */
trait MenuTrait
{

    /**
     * Transformar array to menu.
     *
     * @param array $nav
     */
    private function __createMenu(array $nav): string
    {
        $html = '';
        // iterar sobre los elementos del menú
        foreach ($nav as $k => $v) {
            // ignorar elementos vacíos o que no sean válidos
            if (empty($k) || !is_string($k) || empty($v)) {
                continue;
            }
            // si el elemento del menú es un enlace externo
            if (preg_match('/http/i', $k)) {
                // agregar un enlace
                $html .= '<li class="nav-item"><a class="nav-link" href="' . $k . '">' . ucfirst($v) . '</a></li>';
            } else {
                // obtener la URL base del sitio y la URL actual
                $urlBase = AntCMS::urlBase() . $k;
                $currentUrl = AntCMS::urlCurrent();
                // verificar si el elemento actual del menú está activo
                $active = ($currentUrl == str_replace('/', '', $k)) ? 'class="nav-link active"' : 'class="nav-link"';
                // si el elemento del menú es un submenú
                if (is_array($v)) {
                    // agregar el submenú
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link" role="button" aria-haspopup="true" aria-expanded="false" href="#">' . ucfirst($k) . '</a>';
                    $html .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    $html .= $this->__createMenu($v);
                    $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    // agregar un elemento de menú
                    $html .= '<li class="nav-item"><a ' . $active . ' href="' . $urlBase . '">' . ucfirst($v) . '</a></li>';
                }
            }
        }
        return $html;
    }

    /**
     * Navegación
     *
     * @return void
     */
    public static function menu(): void
    {
        $arr = (array)AntCMS::$config['menu'];
        $html = (string)self::run()->__createMenu($arr);
        echo $html;
    }

}

/**
 * Este es un ejemplo de un trait de PHP llamado "NavFolderTrait", que proporciona una función llamada
 * "navFolder()".
 * Esta función genera un enlace de navegación entre las páginas de una misma carpeta en el CMS.
 * Primero, la función obtiene el segmento de la URL correspondiente a la carpeta actual y verifica si existe y
 * si es una carpeta válida.
 * Si no existe o no es una carpeta válida, no muestra nada.
 * Luego, la función obtiene todas las páginas dentro de la carpeta actual, ordenadas por fecha descendente y
 * excluyendo las páginas index y 404.
 * Si la carpeta tiene menos de dos páginas, no muestra nada.
 * Después, la función obtiene la URL base de la carpeta actual y la URL de la página actual. Luego, crea el
 * inicio del HTML para la navegación.
 * La función recorre cada página de la carpeta, y para cada página obtiene el slug (ruta) de la página actual.
 * Si la página actual no es la página actualmente visitada, continua con la siguiente.
 * La función obtiene las flechas de navegación y las páginas anterior y posterior a la actual.
 * Si la página anterior y posterior existen y están publicadas, crea un enlace a ellas, de lo contrario, crea
 * un enlace deshabilitado.
 * Finalmente, la función agrega los enlaces a la navegación en formato HTML, cierra el HTML para la navegación
 * y lo muestra en pantalla.
 * Este trait se puede usar en cualquier clase que lo necesite, y la función "navFolder()" estará disponible en
 * esa clase.
 */
trait NavFolderTrait
{
    /**
     * Genera un enlace de navegación entre las páginas de una misma carpeta en el CMS.
     * Si la carpeta no existe o tiene menos de dos páginas, no muestra nada.
     * La navegación se muestra en forma de flechas hacia la página anterior y posterior a la actual.
     *
     * @return void
     */
    public static function navFolder(): void
    {
        // Obtiene el segmento de la URL correspondiente a la carpeta actual
        $url_segment = AntCMS::urlSegment(0);

        // Si el segmento no existe o la carpeta no existe, no muestra nada
        if (!$url_segment || !is_dir(CONTENT . '/' . $url_segment)) {
            return;
        }

        // Obtiene las páginas dentro de la carpeta actual, ordenadas por fecha descendente y excluyendo las páginas index y 404
        $pages = AntCMS::run()->pages($url_segment, 'date', 'DESC', ['index', '404']);

        // Si la carpeta tiene menos de dos páginas, no muestra nada
        if (count($pages) <= 2) {
            return;
        }

        // Obtiene la URL base de la carpeta actual y la URL de la página actual
        $url_folder = AntCMS::urlBase() . '/' . $url_segment;
        $url_current = AntCMS::urlCurrent();

        // Crea el inicio del HTML para la navegación
        $html = '<div class="btn-group my-3">';

        // Recorre cada página de la carpeta
        foreach ($pages as $k => $page) {

            // Obtiene el slug de la página actual
            $slug = trim($url_segment . '/' . $page['slug'], '/');

            // Si la página actual no es la página actualmente visitada, continua con la siguiente
            if ($url_current !== $slug) {
                continue;
            }

            // Obtiene las flechas de navegación y las páginas anterior y posterior a la actual
            $leftArrow = self::run()->leftArrow();
            $rightArrow = self::run()->rightArrow();
            $prevPage = $pages[$k - 1] ?? null;
            $nextPage = $pages[$k + 1] ?? null;

            // Crea los enlaces a la página anterior y posterior si existen, sino, solo muestra las flechas
            // Comprueba que este publicada tambien
            $prevLink = ($prevPage && $prevPage['published']) ? '<a class="btn btn-sm btn-light" href="' . $url_folder . '/' . $prevPage['slug'] . '" title="' . $prevPage['title'] . '">' . $leftArrow . '</a>' : '<a class="btn btn-sm btn-light disabled" href="#" disabled>' . $leftArrow . '</a>';
            $nextLink = ($nextPage && $nextPage['published']) ? '<a class="btn btn-sm btn-light" href="' . $url_folder . '/' . $nextPage['slug'] . '" title="' . $nextPage['title'] . '">' . $rightArrow . '</a>' : '<a class="btn btn-sm btn-light disabled" href="#" disabled>' . $rightArrow . '</a>';

            // Agrega los enlaces a la navegación en formato HTML
            $html .= $prevLink . $nextLink;
        }

        // Cierra el HTML para la navegación y lo muestra en pantalla
        $html .= '</div>';
        echo $html;
    }

}

/**
 * Este código define un trait llamado SearchTrait, que proporciona un método para buscar contenido en un sitio
 * web y mostrar los resultados en una lista HTML.
 * El método query es el punto de entrada para la búsqueda. Primero comprueba si se ha enviado una consulta de
 * búsqueda a través de la variable $_GET['buscar'].
 * Si no se ha enviado ninguna consulta, el método se detiene y no se realiza ninguna búsqueda.
 * Si se ha enviado una consulta, se limpia y almacena en una variable $query. Luego, el método obtiene todas
 * las páginas del sitio, ordenadas por fecha descendente, y filtra las que tienen el estado "404". A
 * continuación, filtra las páginas que contienen la consulta de búsqueda en cualquiera de sus campos
 * relevantes, como el título, la descripción, las etiquetas, etc.
 * Después de obtener los resultados, el método llama al método privado __renderQuery, que recibe los
 * resultados como un parámetro y devuelve una lista HTML que contiene cada resultado. Finalmente, el método
 * genera el HTML de salida con el número total de resultados y muestra un mensaje apropiado si no se
 * encontraron resultados.
 * En resumen, este trait proporciona una forma fácil de buscar contenido en un sitio web y mostrar los
 * resultados en una lista HTML.
 */
trait SearchTrait
{
    /**
     * Resultados busqueda
     *
     * @return string
     */
    private function __renderQuery(array $results): string
    {
        // Se inicializa la variable $html con una lista HTML
        $html = '<ul class="list-unstyled">';

        // Se recorren los resultados y se construye un HTML por cada página
        foreach ($results as $page) {
            // Se construye la URL de la página
            $url = AntCMS::urlBase() . rtrim(str_replace('//', '/', str_replace(AntCMS::urlBase(), '', $page['url'])), '/');
            // Se convierte la fecha de la página en formato dd-mm-aaaa
            $dateFormat = date('d-m-Y', (int)$page['date']);
            // Se agrega el HTML de la página a la lista
            $html .= <<<HTML
                <li>
                    <strong>{$dateFormat}</strong>
                    <span> - </span>
                    <a class="text-secondary" href="{$url}">{$page['title']}</a>
                </li>
            HTML;
        }

        // Se cierra la lista HTML y se devuelve el resultado
        $html .= '</ul>';
        return $html;
    }

    /**
     * Buscar
     *
     * @return void
     */
    public static function query(): void
    {
        // Verifica si se ha enviado una consulta de búsqueda
        if (!isset($_GET['buscar'])) {
            return;
        }

        // Limpia y almacena la consulta de búsqueda
        $query = trim($_GET['buscar']);
        if (empty($query)) {
            return;
        }

        // Obtiene todas las páginas del sitio, ordenadas por fecha descendente, y filtra las que tienen el estado "404"
        $pages = AntCMS::run()->pages('/', 'date', 'DESC', ['404'], 0);

        // Filtra las páginas que contienen la consulta de búsqueda en cualquiera de sus campos relevantes
        $results = array_filter($pages, function ($page) use ($query) {
            return preg_match("/$query/i", $page['title']) ||
            preg_match("/$query/i", $page['description']) ||
            preg_match("/$query/i", $page['tags']) ||
            preg_match("/$query/i", $page['author']) ||
            preg_match("/$query/i", $page['keywords']) ||
            preg_match("/$query/i", $page['slug']) ||
            preg_match("/$query/i", date('d-m-Y', (int)$page['date']));
        });

        // Obtiene el número total de resultados y genera el HTML de salida
        $total = count($results);
        $searchOutput = self::run()->__renderQuery($results);
        $html = "<article class='bg-primary text-light p-4'>{$searchOutput}<footer><strong class='text-secondary'>{$total}</strong> resultado/s de {$query}</footer></article>";

        // Si no se encontraron resultados, muestra un mensaje apropiado
        if (!$results) {
            $html = "<article class='bg-primary text-light p-4'><h3 class='fs-4'>No hay resultados de {$query}</h3></article>";
        }

        // Imprime el HTML generado
        echo $html;
    }
}

/**
 * Este código define una clase llamada "Functions" que contiene varias funciones y métodos para el tema.
 * La clase utiliza varias "traits" como "ArticlesTrait", "NavFolderTrait", "MenuTrait", "SearchTrait" y
 * "ContactTrait" para agregar funcionalidad adicional.
 * Hay tres funciones en la clase: "leftArrow()", "rightArrow()" y "run()". Las dos primeras funciones
 * devuelven una cadena de texto que contiene un icono de flecha izquierda o derecha, respectivamente.
 * La función "run()" es un método estático que crea y devuelve una instancia de la clase "Functions".
 * También hay una función "init()" que registra varias funciones en el sistema utilizando la clase "AntCMS".
 * Las funciones registradas son "articles", "navFolder", "navigation", "contact" y "theme_before".
 */
class Functions
{

    use ArticlesTrait;
    use NavFolderTrait;
    use MenuTrait;
    use SearchTrait;
    use ContactTrait;

    /**
     * La función run() es un método estático que devuelve una instancia de la propia clase Functions a través
     * de la palabra clave static. Esta función se utiliza para crear una instancia de la clase Functions sin
     * necesidad de utilizar el constructor, lo que permite crear objetos de forma más eficiente.
     *
     * @return static
     */
    public static function run(): self
    {
        return new static;
    }

    /**
     * Flecha izq.
     *
     * @return string
     * @abstract
     */
    public function leftArrow(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/></svg>';
    }
    /**
     * Flecha drch.
     *
     * @return string
     * @abstract
     */
    public function rightArrow(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/></svg>';
    }

    /**
     * Cargando las funciones
     *
     * @return void
     */
    public static function init(): void
    {
        AntCMS::actionAdd("articles", "Functions::articles");
        AntCMS::actionAdd("navFolder", "Functions::navFolder");
        AntCMS::actionAdd("navigation", "Functions::menu");
        AntCMS::actionAdd("contact", "Functions::contact");
        AntCMS::actionAdd("theme_before", "Functions::query");
    }
}

Functions::init();
