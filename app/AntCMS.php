<?php

declare (strict_types = 1);

define('ROOT', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));
define('SECURE', true);
define('ANTCMS_MINIMUM_PHP', '7.4.0');
define('CONFIG', ROOT . '/app/config');
define('THEME', ROOT . '/public/views');
define('CONTENT', ROOT . '/public/content');
define('DEBUG', true);

// Mostrar errores si dev_mode es true
if (DEBUG) {
    ini_set('display_errors', (string) 1);
    ini_set('display_startup_errors', (string) 1);
    ini_set('track_errors', (string) 1);
    ini_set('html_errors', (string) 1);
    error_reporting(E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set('display_errors', (string) 0);
    ini_set('display_startup_errors', (string) 0);
    ini_set('track_errors', (string) 0);
    ini_set('html_errors', (string) 0);
    error_reporting(0);
}

if (version_compare($ver = PHP_VERSION, $req = ANTCMS_MINIMUM_PHP, '<')) {
    $out = sprintf('Usted esta usando PHP %s, pero AntCMs necesita <strong>PHP %s</strong> para funcionar.', $ver, $req);
    exit($out);
}

/**
 * Ant Template.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 1.0.0
 */
class AntTPL
{

    public $tags = [];
    public $tmp = [];
    public $data = [];
    /**
     * Constructor.
     */
    public function __construct()
    {
        // tags
        $this->tags = [
            // comment
            //{* comment *}
            '{\*(.*?)\*}' => '<?php echo "\n";?>',
            // confitional
            '{If: ([^}]*)}' => '<?php if ($1): ?>',
            '{Else}' => '<?php else: ?>',
            '{Elseif: ([^}]*)}' => '<?php elseif ($1): ?>',
            '{\/If}' => '<?php endif; ?>',
            // loop
            '{Loop: ([^}]*) as ([^}]*)=>([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $2=>$3): ?>',
            '{Loop: ([^}]*) as ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $2): ?>',
            '{Loop: ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $value): ?>',
            '{\/Loop}' => '<?php $counter++; endforeach; ?>',
            // {?= 'hello world' ?}
            '{\?(\=){0,1}([^}]*)\?}' => '<?php if(strlen("$1")) echo $2; else $2; ?>',
            // {? 'hello world' ?}
            '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo %%$1; ?>',
            // capitalize
            '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|capitalize}' => '<?php echo ucfirst(%%$1); ?>',
            // lowercase
            '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|lower}' => '<?php echo strtolower(%%$1); ?>',
        ];

        $this->tmp = ROOT . '/tmp/';
        if (!file_exists($this->tmp)) {
            mkdir($this->tmp);
        }

        $this->removeCacheOneDay();
    }

    /**
     * Remove cache 1 day
     *
     * @return void
     */
    public function removeCacheOneDay(): void
    {
        // Se establece la zona horaria a utilizar
        date_default_timezone_set('Europe/Madrid');

        // Directorio donde se encuentra la caché
        $cache_dir = $this->tmp;

        // Se obtiene la hora actual en formato UNIX timestamp
        $now = time();

        // Se lee el archivo JSON que contiene la fecha de la última limpieza de caché
        $json = file_exists($cache_dir . '/cache_info.json') ? json_decode(file_get_contents($cache_dir . '/cache_info.json'), true) : [];

        // Se obtiene la fecha de la última limpieza de caché, que se almacena en el campo 'date' del archivo JSON
        $last_cleanup = (array_key_exists('date', $json)) ? (int)$json['date'] : 0;

        // Se comprueba si ya ha pasado un día desde la última limpieza de caché
        if ($now - $last_cleanup > 86400) {
            // Se eliminan los archivos HTML almacenados en la caché
            $files = glob($cache_dir . '/*.html');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Se actualiza el archivo JSON con la fecha y hora actual, y la fecha de limpieza
            file_put_contents($cache_dir . '/cache_info.json', json_encode([
                'date' => $now,
                'cleanup' => date("Y-m-d h:m:s", strtotime(date("d-m-Y"))),
            ]));
        }
    }

    /**
     * Callback.
     *
     * @param mixed $variable the var
     *
     * @return array|string
     */
    public function callback($variable)
    {
        if (!is_string($variable) && is_callable($variable)) {
            return $variable();
        }

        return $variable;
    }

    /**
     *  Set var.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return mixed
     */
    public function set(string $name, $value): object
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Append data in array.
     *
     * @param string $name  the key
     * @param string $value the value
     *
     * @return null
     */
    public function append($name, $value)
    {
        $this->data[$name][] = $value;
    }

    /**
     * Parse content.
     *
     * @param string $content el contenido a analizar y procesar
     *
     * @return string el contenido procesado
     */
    private function __parse(string $content): string
    {
        // reemplaza etiquetas con PHP
        foreach ($this->tags as $regexp => $replace) {
            if (false !== strpos($replace, 'self')) {
                $content = preg_replace_callback('#' . $regexp . '#s', $replace, $content);
            } else {
                $content = preg_replace('#' . $regexp . '#', $replace, $content);
            }
        }

        // reemplaza variables
        if (preg_match_all('/(\$(?:[a-zA-Z0-9_-]+)(?:\.(?:(?:[a-zA-Z0-9_-][^\s]+)))*)/', $content, $matches)) {
            for ($i = 0; $i < count($matches[1]); ++$i) {
                // $a.b to $a["b"]
                $rep = $this->__replaceVariable($matches[1][$i]);
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }

        // elimina espacios entre %% y $
        $content = preg_replace('/\%\%\s+/', '%%', $content);

        // llama a cv() para las variables firmadas
        if (preg_match_all('/\%\%(.)([a-zA-Z0-9_-]+)/', $content, $matches)) {
            for ($i = 0; $i < count($matches[2]); ++$i) {
                if ('$' == $matches[1][$i]) {
                    $content = str_replace($matches[0][$i], 'self::callback($' . $matches[2][$i] . ')', $content);
                } else {
                    $content = str_replace($matches[0][$i], $matches[1][$i] . $matches[2][$i], $content);
                }
            }
        }
        return $content;
    }

    /**
     * Ejecuta un archivo de plantilla.
     *
     * @param string $file    La ruta del archivo de plantilla a ejecutar.
     * @param int    $counter Un contador interno para evitar la recursión infinita.
     *
     * @return string El contenido de la plantilla procesada.
     */
    private function __run(string $file, int $counter = 0): string
    {
        // Extrae información de la ruta del archivo de plantilla.
        $pathInfo = pathinfo($file);
        // Genera un nombre de archivo temporal basado en la información de la ruta del archivo original.
        $tmpFile = $this->tmp . $pathInfo['basename'];

        // Verifica si el archivo existe.
        if (!is_file($file)) {
            // Si el archivo no existe, muestra un mensaje de error.
            echo "Plantilla '$file' no encontrada.";
        } else {
            // Lee el contenido del archivo.
            $content = file_get_contents($file);

            // Verifica si el contenido de la plantilla contiene etiquetas que requieren ser procesadas.
            // Si es así, escribe el contenido en un archivo temporal y lo procesa nuevamente.
            if ($this->__searchTags($content) && ($counter < 3)) {
                file_put_contents($tmpFile, $content);
                $content = $this->__run($tmpFile, ++$counter);
            }

            // Procesa las etiquetas y variables de la plantilla.
            file_put_contents($tmpFile, $this->__parse($content));

            // Extrae las variables de la plantilla y las convierte en variables de PHP.
            extract($this->data, EXTR_SKIP);

            // Almacena el contenido generado por la plantilla en un búfer de salida.
            ob_start();
            include $tmpFile;

            // Si la opción de depuración está desactivada, elimina el archivo temporal.
            if (!DEBUG) {
                unlink($tmpFile);
            }

            // Devuelve el contenido generado por la plantilla.
            return ob_get_clean();
        }
    }

    /**
     * Dibujar archivo.
     *
     * @param string $file el archivo
     *
     * @return string el resultado del dibujo
     */
    public function draw(string $file): string
    {
        // Ejecuta el archivo y obtiene el resultado
        $result = $this->__run($file);

        // Si la extensión tidy está cargada, utiliza la función tidy_repair_string para
        // formatear y limpiar el resultado HTML.
        if (extension_loaded('tidy')) {
            return tidy_repair_string($result, [
                'output-xml' => true,
                'indent' => false,
                'wrap' => 0,
            ]);
        } else {
            // Si tidy no está disponible, simplemente devuelve el resultado sin procesar
            return $result;
        }
    }

    /**
     *  Crea un comentario que no sea visible.
     *
     * @param string $content el contenido
     *
     * @return string
     */
    public function comment(string $content): string
    {
        // Devuelve nulo ya que la función no hace nada con el contenido
        return null;
    }

    /**
     *  Busca etiquetas.
     *
     * @param string $content el contenido
     *
     * @return bool
     */
    private function __searchTags(string $content): bool
    {
        // Verifica si el contenido tiene alguna etiqueta definida
        foreach ($this->tags as $regexp => $replace) {
            if (preg_match('#' . $regexp . '#sU', $content, $matches)) {
                return true;
            }
        }
        // Si no se encontró ninguna etiqueta, devuelve false
        return false;
    }

    /**
     * Reemplazar la notación de puntos en una variable con la notación de corchetes.
     *
     * @param string $var la variable en notación de puntos
     *
     * @return string la variable en notación de corchetes
     */
    private function __replaceVariable(string $var): string
    {
        // Verificar si la variable ya está en notación de corchetes
        if (false === strpos($var, '.')) {
            return $var;
        }

        // Reemplazar los puntos por corchetes
        return preg_replace('/\.([a-zA-Z\-_0-9]*(?![a-zA-Z\-_0-9]*(\'|\")))/', "['$1']", $var);
    }
}

trait ActionTrait
{
    /**
     * Creamos una action.
     *
     *  <code>
     *      AntCMS::actionAdd('demo',function(){});
     *  </code>
     *
     * @param string $name
     * @param mixed  $func
     * @param int    $priority
     * @param array  $args
     *
     * @return static
     */
    public static function actionAdd(string $name, callable $func, int $priority = 10, array $args = null): void
    {
        // Agregamos la funcion
        static::$__actions[] = [
            'name' => $name,
            'func' => $func,
            'priority' => $priority,
            'args' => $args,
        ];
    }

    /**
     * Llamamos una action.
     *
     *  <code>
     *      AntCMS::actionRun('demo',array());
     *  </code>
     *
     * @param string $name
     * @param array  $args
     *
     * @return void
     */
    public static function actionRun(string $name, array $args = []): void
    {
        if (count(static::$__actions) > 0) {
            // Ordenar las acciones por prioridad
            $actions = self::shortArray(static::$__actions, 'priority');
            // Bucle a través de $actions matriz
            foreach ($actions as $action) {
                // Ejecutar una acción específica
                if ($action['name'] == $name) {
                    // isset argumentos ?
                    if (isset($args)) {
                        // Devolver o representar resultados de acciones específicas
                        call_user_func_array($action['func'], $args);
                    } else {
                        call_user_func_array($action['func'], $action['args']);
                    }
                }
            }
        }
    }
}

trait ArrTrait
{
    /**
     *  Array short.
     *
     * @param array $a      array
     * @param mixed $subkey mixed
     * @param mixed $order  mixed
     *
     * @return array
     */
    public static function shortArray(array $a = array(), string $subkey = "", string $order = null): array
    {
        if (count($a) != 0 || (!empty($a))) {
            foreach ($a as $k => $v) {
                // si resulta ser string convertir a minúsculas
                if (is_string($v[$subkey])) {
                    $b[$k] = strtolower((string)$v[$subkey]);
                } else {
                    $b[$k] = $v[$subkey];
                }
            }
            // Orden ascendente
            if ($order == null || $order == 'ASC') {
                asort($b);
                // Orden descendente
            } elseif ($order == 'DESC') {
                arsort($b);
            }

            foreach ($b as $key => $val) {
                $c[] = $a[$key];
            }

            return $c;
        }
    }
}

trait ErrorTrait
{

    /**
     * Obtiene Error en no publicado..
     *
     * @param object $Tpl
     *
     * @return void
     */
    public function errorPage(object $Tpl): void
    {
        $page = array(
            'title' => self::$config['notPublished']['title'],
            'description' => self::$config['notPublished']['description'],
            'robots' => 'noindex,nofollow',
            'content' => self::$config['notPublished']['content'],
            'tags' => '404',
            'author' => self::$config['author'],
            'image' => '', // href file
            'date' => date('d-m-Y'),
            'keywords' => self::$config['keywords'],
            'category' => 'empty',
            'background' => 'white', // blue, #f55,rgb(0,0,0)
            'video' => '', // src file
            'color' => 'black', // blue, #f55,rgb(0,0,0)
            'css' => '', // src file
            'javascript' => '', // src file
            'attrs' => [1, 2, 3], // = [1,2,true,'string']
            'json' => '', // = json file
        );
        $Tpl->set('page', $page);
        $Tpl->set('config', self::$config);
        echo $Tpl->draw(THEME . '/404.html');
    }
}

trait EvalPhpTrait
{
    /**
     * Evalúa las etiquetas {php} y las sustituye por el resultado de la evaluación.
     *
     * @param string $str el string con las etiquetas {php}
     *
     * @return string el string con las etiquetas {php} sustituidas por el resultado de la evaluación
     */
    protected static function _evalPHP(string $str): string
    {
        // Utiliza una expresión regular para buscar todas las etiquetas {php} en $str, y luego utiliza
        // la función '_obEval' para evaluar el código PHP entre esas etiquetas y devolver su resultado.
        return preg_replace_callback('/\\{php\\}(.*?)\\{\\/php\\}/ms', 'AntCMS::_obEval', $str);
    }

    /**
     * Eval Content.
     *
     * @param string $data la cadena de texto que contiene el código PHP a ejecutar
     *
     * @return string el resultado de la evaluación del código PHP
     */
    protected static function _obEval(string $data): string
    {
        // Comenzar el almacenamiento en el búfer de salida
        ob_start();

        // Ejecutar el contenido de la cadena `$data` como código PHP
        eval($data);

        // Obtener el contenido almacenado en el búfer de salida y almacenarlo en la variable `$data`
        $data = ob_get_contents();

        // Detener el almacenamiento en el búfer de salida y limpiar el búfer
        ob_end_clean();

        // Retornar la variable `$data` que contiene el resultado de la evaluación
        return $data;
    }
}

trait FileTrait
{

    /**
     * Obtener archivos
     *
     * @param string $name el nombre del archivo a obtener
     *
     * @return void
     */
    public static function getFile(string $name): void
    {
        // Verifica si el archivo existe y es un archivo regular
        if (file_exists($name) && is_file($name)) {
            // Imprime el contenido del archivo en el buffer de salida
            echo file_get_contents($name);
        } else {
            // Si el archivo no existe o no es un archivo regular, lanza una excepción con un mensaje de error
            throw new Exception('No existe el archivo ' . $name);
        }
    }

}

trait FilterTrait
{

    /**
     * Aplicar filtro.
     *
     * @param string $filter_name Nombre del filtro a aplicar
     * @param string $value Valor a filtrar
     *
     * @return string Valor filtrado
     */
    public static function applyFilter(string $filter_name, string $value): string
    {
        // Obtener argumentos adicionales
        $args = array_slice(func_get_args(), 2);

        // Si no existe el filtro, retornar el valor sin filtrar
        if (!isset(static::$_filters[$filter_name])) {
            return $value;
        }

        // Aplicar cada función del filtro en orden de prioridad
        foreach (static::$_filters[$filter_name] as $priority => $functions) {
            if (!is_null($functions)) {
                foreach ($functions as $function) {
                    $all_args = array_merge([$value], $args);
                    $function_name = $function['function'];
                    $accepted_args = $function['accepted_args'];

                    // Preparar los argumentos para la llamada a la función
                    if (1 == $accepted_args) {
                        $the_args = [$value];
                    } elseif ($accepted_args > 1) {
                        $the_args = array_slice($all_args, 0, $accepted_args);
                    } elseif (0 == $accepted_args) {
                        $the_args = null;
                    } else {
                        $the_args = $all_args;
                    }

                    // Llamar a la función del filtro con los argumentos preparados
                    $value = call_user_func_array($function_name, $the_args);
                }
            }
        }

        // Retornar el valor filtrado
        return $value;
    }

    /**
     * Agrega una función como filtro a un nombre de filtro específico.
     *
     * @param string $filter_name     El nombre del filtro al que se agregará la función.
     * @param string $function_to_add La función que se agregará como filtro.
     * @param int    $priority        La prioridad del filtro en relación con otros filtros.
     * @param int    $accepted_args   El número de argumentos que la función de filtro puede aceptar.
     *
     * @return bool True si se agregó el filtro con éxito, false si no.
     */
    public static function setFilter(string $filter_name, string $function_to_add, int $priority = 10, int $accepted_args = 1): bool
    {
        // Comprobar si ya se ha agregado el mismo filtro con la misma prioridad
        if (isset(static::$_filters[$filter_name]["$priority"])) {
            foreach (static::$_filters[$filter_name]["$priority"] as $filter) {
                if ($filter['function'] == $function_to_add) {
                    return true; // Ya se ha agregado la función, salir sin hacer nada
                }
            }
        }

        // Agregar la función al filtro
        static::$_filters[$filter_name]["$priority"][] = [
            'function' => $function_to_add,
            'accepted_args' => $accepted_args,
        ];

        // Ordenar los filtros según la prioridad
        ksort(static::$_filters[$filter_name]["$priority"]);

        return true;
    }

}

trait LoadTrait
{

    /**
     * Carga plantilla
     *
     * @param string $route Ruta del archivo que contiene la plantilla
     *
     * @return void
     */
    protected function _loadTemplating(string $route): void
    {
        // Verificar si el archivo existe y es un archivo regular
        if (file_exists($route) && is_file($route)) {
            // Cargar la plantilla usando require_once y almacenarla en static::$templating
            static::$templating = (require_once $route);
        } else {
            // Si el archivo no existe, lanzar una excepción con un mensaje de error personalizado y un código de error 100
            throw new InvalidArgumentException('Oops.. ¿Dónde está el archivo de la plantilla de templating?!', 100);
        }
    }

    /**
     * Cargar configuración.
     *
     * @param string $route Ruta del archivo de configuración.
     *
     * @return void
     * @throws InvalidArgumentException Si el archivo no existe.
     */
    protected function _loadConfig(string $route): void
    {
        if (!file_exists($route) || !is_file($route)) {
            throw new InvalidArgumentException('El archivo de configuración no existe o no es un archivo válido');
        }

        static::$config = require $route;
    }

    /**
     * Carga las funciones de la plantilla.
     *
     * @return void
     */
    protected function _loadThemeFunctions(): void
    {
        // Ruta del archivo de funciones de la plantilla
        $template_functions = __DIR__ . '/Functions.php';

        // Verificar si existe el archivo de funciones
        if (file_exists($template_functions) && is_file($template_functions)) {
            // Cargar el archivo de funciones
            require_once $template_functions;
        } else {
            // Lanzar una excepción si el archivo de funciones no existe
            throw new InvalidArgumentException('Error: no se pudo encontrar el archivo de funciones de la plantilla.');
        }
    }

}

trait PagesTrait
{

    public function parseUrl(string $site_url, string $page): string
    {
        return preg_replace(
            [
                '/^' . preg_quote(CONTENT, '/') . '/',
                '/index.html$/',
                '/\.html$/',
                '/\\\\+/',
                '/\/$/',
            ],
            [
                $site_url,
                '',
                '',
                '/',
                '',
            ],
            $page
        );
    }

    /**
     *  Obtener array de páginas.
     *
     * <code>
     *  AntCMS::pages('blog','date','DESC',array('index','404'),null);
     * </code>
     *
     * @param string $url
     * @param string $order_by
     * @param string $order_type
     * @param array  $ignore
     * @param int    $limit
     *
     * @return array
     */
    public function pages(string $url, string $order_by = 'date', string $order_type = 'DESC', array $ignore = ['404'], int $limit = 0): array
    {
        // obtener un array de encabezados
        $headers = $this->__headers;

        // escanear carpeta content
        $pages = self::scanFiles(CONTENT . '/' . $url, 'html');

        $_pages = array_filter(array_map(function ($key, $page) use ($ignore, $headers) {
            // ignorar los que no queramos
            if (in_array(basename($page, '.html'), $ignore)) {
                return null;
            }

            // obtenemos el contenido
            $content = file_get_contents($page);
            // lo dividimos en una array
            $_headers = explode(self::SEPARATOR, $content);

            $pageData = [];
            // bucle del array
            foreach ($headers as $campo => $regex) {
                if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                    $pageData[$campo] = trim($match[1]);
                } else {
                    $pageData[$campo] = '';
                }
            }

            // Si el usuario no escribe Fecha, usamos filetime
            if (!$pageData['date']) {
                $pageData['date'] = filemtime($page);
            } else {
                $date = str_replace('/', '-', $pageData['date']);
                $pageData['date'] = strtotime($date);
            }

            // convertir local a url
            $site_url = rtrim(self::urlBase(), '/');
            // Obtener la URL de la página
            $url = $this->parseUrl($site_url, $page);

            $pageData['content'] = $this->_parseContent($content);
            $pageData['url'] = $url;
            $pageData['slug'] = basename($page, '.html');

            return $pageData;
        }, array_keys($pages), $pages));

        // ordenar por campo
        usort($_pages, function ($a, $b) use ($order_by, $order_type) {
            if ($order_type === 'DESC') {
                return strtotime((string)$b[$order_by]) - strtotime((string)$a[$order_by]);
            } else {
                return strtotime((string)$a[$order_by]) - strtotime((string)$b[$order_by]);
            }
        });

        // limitar el número de resultados
        if ($limit > 0) {
            $_pages = array_slice($_pages, 0, $limit);
        }

        return $_pages;
    }

    /**
     * Obtiene la página solicitada.
     *
     * <code>
     *   AntCMS::page('blog');
     * </code>
     *
     * @param string $url
     * @return array
     */
    public function page(string $url): array
    {
        $headers = $this->__headers;
        $content = '';
        $slug = '';
        $date = '';

        // Determinar la ubicación del archivo
        if ($url) {
            $file = CONTENT . '/' . $url;
            if (is_dir($file)) {
                $file .= '/index.html';
            } else {
                $file .= '.html';
            }
        } else {
            $file = CONTENT . '/index.html';
        }

        // Cargar el contenido del archivo
        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $file = CONTENT . '/404.html';
            if (file_exists($file)) {
                $content = file_get_contents($file);
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } elseif (file_exists(CONTENT . '/' . self::urlSegment(0) . '/404.html')) {
                $content = file_get_contents(CONTENT . '/' . self::urlSegment(0) . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            } else {
                $content = file_get_contents(CONTENT . '/' . self::$config['lang'] . '/404.html');
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }

        // Parsear los headers de la página
        $_headers = explode(self::SEPARATOR, $content);
        foreach ($headers as $campo => $regex) {
            if (preg_match('/^[ \\t\\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $_headers[0], $match) && $match[1]) {
                $page[$campo] = trim($match[1]);
            } else {
                $page[$campo] = '';
            }
        }

        // Obtener la URL de la página
        $site_url = rtrim(self::urlBase(), '/');
        // Obtener la URL de la página
        $url = $this->parseUrl($site_url, $file);
        $pages['url'] = $url;

        // Obtener la fecha de la página
        if (!$page['date']) {
            $date = filemtime((string)$page['date']);
        } else {
            $date = strtotime(str_replace('/', '-', $page['date']));
        }
        $page['date'] = date('d-m-Y', (int)$date);

        // Obtener el contenido de la página
        $_content = $this->_parseContent($content);
        if (is_array($_content)) {
            $page['content_short'] = $_content['content_short'];
            $page['content'] = $_content['content_full'];
        } else {
            $page['content_short'] = $_content;
            $page['content'] = $_content;
        }

        // Obtener el slug de la página
        $page['slug'] = basename($file, '.html');

        return $page;
    }

}

trait ParseTrait
{

    /**
     * Parsear contenido.
     *
     * @param string $content el contenido
     *
     * @return $content (array)
     */
    protected function _parseContent(string $content): string
    {
        $_content = '';
        $i = 0;
        // Dividir el contenido en un array basado en la constante SEPARATOR
        // y volver a concatenar todo el contenido excepto el primer elemento
        foreach (explode(self::SEPARATOR, $content) as $c) {
            0 != $i++ and $_content .= $c;
        }
        $content = $_content;
        // Reemplazar las variables del contenido con los valores correspondientes
        $content = str_replace('{Url}', self::urlBase(), $_content);
        $content = str_replace('{Email}', self::$config['email'], $content);
        // Encontrar la posición de la etiqueta de resumen {More} y separar el contenido
        // en dos partes: contenido corto y contenido completo
        $pos = strpos($content, '{More}');
        if (false === $pos) {
            // Aplicar un filtro a todo el contenido
            $content = static::applyFilter('content', $content);
        } else {
            $content = explode('{More}', $content);
            // Aplicar un filtro al contenido corto
            $content['content_short'] = self::applyFilter('content', $content[0]);
            // Aplicar un filtro al contenido completo
            $content['content_full'] = self::applyFilter('content', $content[0] . $content[1]);
        }
        // Eliminar espacios en blanco extra
        //$content = preg_replace('/\s+/', ' ', $content);
        // Evaluar cualquier código PHP en el contenido
        $content = static::_evalPHP($content);

        return $content;
    }
}

trait RunTrait
{
    /**
     * Retorna una nueva instancia de la clase actual.
     *
     * @return new static
     */
    public static function Run(): object
    {
        return new static();
    }

}

trait ScanFileTrait
{
    /**
     * Escanea los archivos en un directorio y devuelve una lista de archivos que coinciden con el tipo especificado.
     *
     * @param string $folder el directorio a escanear
     * @param string $type el tipo de archivo que se va a buscar
     * @param bool $file_path si se debe devolver el nombre del archivo con la ruta completa o solo el nombre del archivo
     *
     * @return mixed la lista de archivos que coinciden con el tipo especificado o `false` si el directorio no existe
     */
    public static function scanFiles(string $folder, string $type = 'html', bool $file_path = true)
    {
        $data = [];
        if (is_dir($folder)) {
            // Crea un iterador recursivo para recorrer los archivos en el directorio
            foreach ($iterator = new \RecursiveIteratorIterator (
                new \RecursiveDirectoryIterator ($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $file) {
                if (null !== $type) {
                    if (is_array($type)) {
                        // Comprueba si el tipo de archivo está en la lista de tipos especificados
                        $file_ext = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($file_ext, $type)) {
                            // Comprueba si el nombre del archivo coincide con el tipo especificado
                            if (strpos($file->getFilename(), $file_ext, 1)) {
                                // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                                if ($file_path) {
                                    $data[] = $file->getPathName();
                                } else {
                                    $data[] = $file->getFilename();
                                }
                            }
                        }
                    } else {
                        // Comprueba si el nombre del archivo coincide con el tipo especificado
                        if (strpos($file->getFilename(), $type, 1)) {
                            // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                            if ($file_path) {
                                $data[] = $file->getPathName();
                            } else {
                                $data[] = $file->getFilename();
                            }
                        }
                    }
                } else {
                    // Añade el nombre del archivo con la ruta completa o solo el nombre del archivo según la opción especificada
                    if ('.' !== $file->getFilename() && '..' !== $file->getFilename()) {
                        if ($file_path) {
                            $data[] = $file->getPathName();
                        } else {
                            $data[] = $file->getFilename();
                        }
                    }
                }
            }
            return $data;
        } else {
            // Si el directorio no existe, devuelve `false`
            return false;
        }
    }
}

trait TextTrait
{
    /**
     *  Acortar texto.
     *
     *  @param string $text El texto que se quiere acortar.
     *  @param int $chars_limit El límite máximo de caracteres que se permiten en el texto.
     *
     *  @return string El texto acortado.
     */
    public static function short(string $text, int $chars_limit): string
    {
        // Comprueba si la longitud del texto es mayor que el límite de caracteres
        if (strlen($text) > $chars_limit) {
            // Elimina todas las etiquetas HTML del texto
            $text = strip_tags($text);
            // Si hay un bug con la letra "ñ" en el texto, se puede descomentar esta línea para solucionarlo
            //$text = htmlentities(html_entity_decode($text));
            // Si la longitud del texto es mayor que el límite de caracteres, lo acorta
            $new_text = substr($text, 0, $chars_limit);
            // Recorta cualquier espacio en blanco al final del texto acortado
            $new_text = trim($new_text);
            // Agrega puntos suspensivos al final del texto acortado para indicar que el texto continúa
            return $new_text . '...';
        } else {
            // Si la longitud del texto es menor que el límite de caracteres, devuelve el texto sin cambios
            return strip_tags($text);
        }
    }

}

trait UrlTrait
{
    /**
     * Obtener url base
     *
     * Esta función devuelve la URL base de la aplicación. Si la aplicación se está ejecutando en localhost,
     * la URL base se tomará del archivo de configuración. Si la aplicación se está ejecutando en un servidor
     * remoto, la URL base se construirá a partir de la información del servidor.
     *
     * @return string url
     */
    public static function urlBase(): string
    {
        // lista blanca de direcciones IP para localhost
        $whitelist = [
            '127.0.0.1',
            '::1',
        ];

        // Comprobar si la aplicación se está ejecutando en localhost
        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            // construir la URL base usando información del servidor
            $https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
            return $https . rtrim(rtrim($_SERVER['HTTP_HOST'], '\\/') . dirname($_SERVER['PHP_SELF']), '\\/');
        } else {
            // obtener la URL base desde la configuración
            return static::$config['url'];
        }
    }

    /**
     * Obtener url actual
     *
     * <code>
     *  AntCMS::urlCurrent();
     * </code>
     *
     * @return string
     */
    public static function urlCurrent(): string
    {
        $url = ''; // Inicializa la variable $url como una cadena vacía
        $request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''; // Obtiene la URI de la solicitud, o vacío si no se establece
        $script_url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : ''; // Obtiene la URL del script actual, o vacío si no se establece

        // Si la URI de la solicitud y la URL del script actual no son iguales
        if ($request_url != $script_url) {
            // Remueve el nombre del script actual de la URI de la solicitud
            $url = trim(preg_replace('/' . str_replace('/', '\\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
        }

        // Remueve cualquier cadena de consulta en la URL
        $url = preg_replace('/\\?.*/', '', $url);

        return $url; // Devuelve la URL actual
    }

    /**
     * Segmentos url
     *
     * <code>
     *  AntCMS::urlSegments();
     * </code>
     *
     * @return array
     */
    public static function urlSegments(): array
    {
        // Primero, obtenemos la URL actual a través de la función urlCurrent().
        // Esta función elimina la parte de la URL que corresponde al script PHP y devuelve el resto de la URL.
        $url = self::urlCurrent();

        // Luego, dividimos la URL en segmentos usando la función explode().
        // La función explode() toma dos argumentos: el delimitador y la cadena que se dividirá en segmentos.
        // En este caso, usamos el delimitador '/' para dividir la URL en segmentos.
        // La función explode() devuelve un array de segmentos.
        return explode('/', $url);
    }

    /**
     * Segmento url
     *
     * <code>
     *  AntCMS::urlSegment(1);
     * </code>
     *
     * @param int $num El número del segmento que se desea obtener. 0 devuelve el primer segmento.
     *
     * @return string El segmento de la URL correspondiente al número proporcionado. Si el segmento no existe, devuelve una cadena vacía.
     */
    public static function urlSegment(int $num = 0): string
    {
        // Obtener todos los segmentos de la URL actual
        $segments = self::UrlSegments();
        // Devolver el segmento que se corresponde con el número proporcionado, si existe.
        return isset($segments[$num]) ? $segments[$num] : '';
    }

    /**
     *  Sanitizar Url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function urlSanitize(string $url): string
    {
        $url = trim($url);
        $url = rawurldecode($url);

        // Define los caracteres especiales a reemplazar
        $special_chars = ['--' => '-', '&quot;' => '-', '!' => '', '@' => '', '#' => '', '$' => '', '%' => '', '^' => '', '*' => '', '(' => '', ')' => '', '+' => '', '{' => '', '}' => '', '|' => '', ':' => '', '"' => '', '<' => '', '>' => '', '[' => '', ']' => '', '\\' => '', ';' => '', "'" => '', ',' => '', '*' => '', '+' => '', '~' => '', '`' => '', 'laquo' => '', 'raquo' => '', ']>' => '', '&#8216;' => '', '&#8217;' => '', '&#8220;' => '', '&#8221;' => '', '&#8211;' => '', '&#8212;' => ''];

        // Realizar la sustitución de caracteres especiales
        $url = strtr($url, $special_chars);

        $url = str_replace('--', '-', $url);
        $url = rtrim($url, '-');
        $url = str_replace('..', '', $url);
        $url = str_replace('//', '', $url);
        $url = preg_replace('/^\//', '', $url);
        $url = preg_replace('/^\./', '', $url);

        return $url;
    }

    /**
     * Corremos la sanitización.
     *
     * @return void
     */
    public static function runSanitize(): void
    {
        $_GET = array_map(['AntCMS', 'urlSanitize'], $_GET);
    }

}

/**
 * AntCMS.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
class AntCMS
{
    use ActionTrait;
    use UrlTrait;
    use ArrTrait;
    use FileTrait;
    use TextTrait;
    use FilterTrait;
    use PagesTrait;
    use ErrorTrait;
    use RunTrait;
    use ScanFileTrait;
    use ParseTrait;
    use LoadTrait;
    use EvalPhpTrait;

    // separador de datos y contenido
    private const SEPARATOR = '----';

    public static $config;
    // public templating
    public static $templating;

    protected static $_filters = [];
    private static $__actions = [];

    // Variables cabecera
    private $__headers = [
        'title' => 'Title',
        'description' => 'Description',
        'tags' => 'Tags',
        'author' => 'Author',
        'image' => 'Image', // href file
        'date' => 'Date',
        'robots' => 'Robots',
        'keywords' => 'Keywords',
        'category' => 'Category',
        'template' => 'Template', // index,post
        'published' => 'Published', // true, false
        'background' => 'Background', // blue, #f55,rgb(0,0,0)
        'video' => 'Video', // src file
        'color' => 'Color', // blue, #f55,rgb(0,0,0)
        'css' => 'Css', // src file
        'javascript' => 'Javascript', // src file
        'attrs' => 'Attrs', // = [1,2,true,'string']
        'json' => 'Json', // = json file
    ];

    /**
     * Iniciar AntCMS.
     *
     * @param string $path
     *
     * @return void
     */
    public function init(
        string $configFile,
        string $templateConfigFile
    ): void{
        // Cargamos la configuracion
        $this->_loadConfig($configFile);
        $this->_loadTemplating($templateConfigFile);
        $this->_loadThemeFunctions();

        // Zona horaria
        @ini_set('date.timezone', static::$config['timezone']);
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set(static::$config['timezone']);
        } else {
            putenv('TZ=' . static::$config['timezone']);
        }

        // Sanitizamos
        self::runSanitize();

        // Dar formato a la fecha
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        // Cabeceras de seguridad
        header("X-Powered-By: Moncho Varela :)");
        header('Strict-Transport-Security: max-age=31536000');
        header("Content-Security-Policy: img-src  'self' data:; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline'");
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer-when-downgrade');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        // charset
        header('Content-Type: text/html; charset=' . static::$config['charset']);

        // recomendable para caracteres en español
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(static::$config['charset']);
        function_exists('mb_internal_encoding') and mb_internal_encoding(static::$config['charset']);

        // Session start
        !session_id() and @session_start();

        // Inicializmos AntTPL
        $AntTpl = new AntTPL();
        // Agregamos las etiquetas de config
        $AntTpl->tags = static::$templating;

        // Cargamos la pagina actual
        $page = $this->page(self::urlCurrent());

        // meta tag generator
        self::actionAdd('meta', fn() => print('<meta name="generator" content="Creado con AntCMS" />'));

        $pageHeadersArray = [
            'title' => $page['title'] ?? static::$config['title'],
            'tags' => $page['tags'] ?? static::$config['keywords'],
            'description' => $page['description'] ?? static::$config['description'],
            'author' => $page['author'] ?? static::$config['author'],
            'image' => $page['image'] ?? '',
            'date' => $page['date'] ?? '',
            'robots' => $page['robots'] ?? 'index,follow',
            'published' => $page['published'] ?? false,
            'keywords' => $page['keywords'] ?? static::$config['keywords'],
            'category' => $page['category'] ?? '',
            'background' => $page['background'] ?? '',
            'video' => $page['video'] ?? '',
            'color' => $page['color'] ?? '',
            'css' => $page['css'] ?? '',
            'javascript' => $page['javascript'] ?? '',
            'attrs' => json_decode($page['attrs'] ?? '{"title":"Hello World"}', true),
            'json' => $page['json'] ?? '',
            'template' => $page['template'] ?? 'index',
        ];

        $page[] = $pageHeadersArray;
        $config = self::$config;

        // Segmento
        $AntTpl->set('Segment', self::urlSegment(0));
        // Publicado
        $page['published'] = ($page['published'] == 'true') ? true : false;
        // Comprobamos que este publicado
        if ($page['published']) {
            $AntTpl->set('page', $page);
            $AntTpl->set('config', $config);
            // Comprueba si existe una plantilla definida y si no usa index.html
            $themeFile = THEME . '/' . $page['template'] . '.html';
            if (file_exists($themeFile) and is_file($themeFile)) {
                die($AntTpl->draw($themeFile));
                exit();
            } else {
                die($AntTpl->draw(THEME . '/index.html'));
                exit();
            }
        } else {
            $AntTpl->set('page', $page);
            $AntTpl->set('config', $config);
            $this->errorPage($AntTpl);
        }
    }
}
