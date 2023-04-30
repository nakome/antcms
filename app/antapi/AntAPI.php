<?php
/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace AntAPI;

use AntCms\AntCMS as AntCMS;

defined('SECURE') or die('No tiene acceso al script.');

if (!function_exists('getUserIP')) {
    function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}
//https://stackoverflow.com/questions/7497733/how-can-i-use-php-to-check-if-a-directory-is-empty
if (!function_exists('is_dir_empty')) {
    /**
     * Check dir empty
     *
     * @param string $dir
     */
    function is_dir_empty($dir)
    {
        if (!is_readable($dir)) {
            return null;
        }

        return (count(scandir($dir)) == 2);
    }
}

if (!function_exists('jsonOutput')) {
    /**
     * Json output
     *
     * @param array $arr
     *
     * @return array
     */
    function jsonOutput(array $arr): void
    {
        cors();
        @header('Content-Type: application/json');
        exit(json_encode($arr));
    }
}

if (!function_exists('createArray')) {
    /**
     * Map
     *
     * @param array $item
     *
     * @return array
     */
    function createArray(array $item): array
    {
        return [

            'title' => $item['title'],
            'description' => $item['description'],
            'tags' => $item['tags'],
            'author' => $item['author'],
            'image' => $item['image'],
            'date' => $item['date'],
            'robots' => $item['robots'],
            'keywords' => $item['keywords'],
            'category' => $item['category'],
            'template' => $item['template'],
            'published' => $item['published'],
            'background' => $item['background'],
            'video' => $item['video'],
            'color' => $item['color'],
            'css' => $item['css'],
            'javascript' => $item['javascript'],
            'attrs' => $item['attrs'],
            'json' => $item['json'],
            'url' => isset($item['url']) ? $item['url'] : '',
            'slug' => $item['slug'],
            'content' => isset($item['content']) ? $item['content'] : '',

        ];
    }
}

if (!function_exists('map')) {
    /**
     * Map
     *
     * @param array $arr
     *
     * @return array
     */
    function map(array $arr): array
    {
        return array_map(fn($item) => createArray($item), $arr);
    }
}

if (!function_exists('imgToDataUri')) {
    /**
     * Image to data-uri
     *
     * @param string $url
     */
    function imgToDataUri($url) {
        // Get extension
        $tipo = pathinfo($url, PATHINFO_EXTENSION);
        // read image
        $data = base64_encode(file_get_contents($url));
        // Create data-uri
        $dataUri = 'data:image/' . $tipo . ';base64,' . $data;
        return $dataUri;
    }
}

if (!function_exists('textToDataUri')) {
    /**
     * Text to data-uri
     *
     * @param string $url
     */
    function textToDataUri($url) {
        // read image
        $data = base64_encode(file_get_contents($url));
        // Create data-uri
        $dataUri = 'data:text/plain;base64,' . $data;
        return $dataUri;
    }
}


if (!function_exists('cors')) {
    /**
     * C.O.R.S.
     *
     * @return <type>
     */
    function cors()
    {
        // Permitir que desde cualquier origen
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide si el origen en $_SERVER['HTTP_ORIGIN'] es uno
            // que quieres permitir, y si es así:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // cache por 1 dia
        }
        // Los encabezados de Control de Acceso se reciben durante las solicitudes de OPCIONES
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }
}

if (array_key_exists('api', $_GET)) {
    // Get config
    AntCMS::$config = include ROOT . '/app/config/default.php';
    $config = AntCMS::$config;
    // method
    $method = ($_GET['api']) ? $_GET['api'] : null;

    // cors
    cors();

    // Switch method
    switch ($method) {
        //[url]?api=pages
        case 'pages':
            //?api=pages&name=blog&limit=6&order=slug
            if (array_key_exists('name', $_GET)) {
                $name = $_GET['name'];
                // limit
                $limit = array_key_exists('limit', $_GET) ? (int)$_GET['limit'] : (int) 0;
                // order
                $order = array_key_exists('order', $_GET) ? (string)$_GET['order'] : 'date';
                // folder
                $folder = ROOT . "/public/content/{$name}";
                // check if is dir
                if (is_dir($folder) && !is_dir_empty($folder)) {
                    $pages = AntCMS::run()->pages($name, $order, 'DESC', ['index', '404'], $limit);
                    jsonOutput([
                        'STATUS' => true,
                        'IP' => getUserIP(),
                        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                        'PARAMS' => $_GET,
                        'DATA' => is_array($pages) ? map($pages) : [],
                    ]);
                } else {
                    jsonOutput([
                        'STATUS' => true,
                        'IP' => getUserIP(),
                        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                        'PARAMS' => $_GET,
                        'DATA' => [],
                    ]);
                }
            } else {
                $pages = AntCMS::run()->pages('', 'date', 'DESC', ['404']);
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => is_array($pages) ? map($pages) : [],
                ]);
            }
            break;

        case 'page':
            //?api=page&name=blog
            if (array_key_exists('name', $_GET)) {
                $name = $_GET['name'];
                $file = ROOT . "/public/content/{$name}.html";
                if (file_exists($file) && is_file($file)) {
                    jsonOutput([
                        'STATUS' => true,
                        'IP' => getUserIP(),
                        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                        'PARAMS' => $_GET,
                        'DATA' => createArray(
                            AntCMS::run()->page($name)
                        ),
                    ]);
                } elseif (
                    file_exists(ROOT . "/public/content/$name/index.html") &&
                    is_file(ROOT . "/public/content/$name/index.html")
                ) {
                    jsonOutput(
                        createArray(
                            AntCMS::run()->page($name),
                            $name
                        )
                    );
                } else {
                    jsonOutput([
                        'STATUS' => true,
                        'IP' => getUserIP(),
                        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                        'PARAMS' => $_GET,
                        'DATA' => [],
                    ]);
                }
            } else {
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => [],
                ]);
            }
            break;
        case 'images':
            if (array_key_exists('url', $_GET)) {
                $url = $_GET['url'];
                $img = imgToDataUri($url);
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => [
                        'url' => $url,
                        'image' => $img
                    ],
                ]);
            } else {
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => [],
                ]);
            }
            break;
        case 'text':
            if (array_key_exists('src', $_GET)) {
                $url = $_GET['src'];
                $src = textToDataUri($url);
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => [
                        'url' => $url,
                        'src' => $src
                    ],
                ]);
            } else {
                jsonOutput([
                    'STATUS' => true,
                    'IP' => getUserIP(),
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    'PARAMS' => $_GET,
                    'DATA' => [],
                ]);
            }
            break;
        default:
            $info = <<<HTML
                <!Doctype html>
                <html>
                    <head>
                        <title>AntCMS Api</title>
                        <style rel="stylesheet">*{box-sizing:border-box;}body{margin:20px auto;max-width:800px;line-height:1.5;font-family:system-ui, -apple-system, "Segoe UI", "Roboto", "Ubuntu", "Cantarell", "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"; font-size:16px;font-weight:300;background:#242424;color:#fff;}ul{list-style:none;margin:0;padding:0}code{padding:2px 3px;border-radius:3px;background:#fff;color:#242424;margin:0 10px}code a{text-decoration:none;color:#242424;}</style>
                    </head>
                    <body>
                        <h1>AntCMS Api</h1>
                        <p>Urls de ayuda.</p>
                        <ul style="list-style:none;margin:0;padding:0;">
                            <li><strong>Pages: </strong> <code><a href="?api=pages">[url]?api=pages</a></code></li>
                            <li><strong>Pages like blog: </strong><code><a href="?api=pages&name=blog">[url]?api=pages&name=blog</a></code></li>
                            <li><strong>Pages like blog with limit: </strong><code><a href="?api=pages&name=blog&limit=6">[url]?api=pages&name=blog&limit=6</a></code></li>
                            <li><strong>Pages pages like blog with limit and order: </strong><code><a href="?api=pages&name=blog&limit=6&order=slug">[url]?api=pages&name=blog&limit=6&order=slug</a></code></li>
                            <li><strong>--------------</strong></li>
                            <li>Convert image to data-uri</li>
                            <li><strong>Images:</strong> <code>[url]?api=images&url=[image-url]</code></li>
                            <li>Convert text to data-uri</li>
                            <li><strong>Images:</strong> <code>[url]?api=text&src=[text-url]</code></li>
                        </ul>
                    </body>
                </html>
            HTML;
            die($info);
            break;
    }
}
