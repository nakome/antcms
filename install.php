<?php

declare (strict_types = 1);

// Directorio de la carpeta
define('ROOT_DIR', rtrim(dirname(__FILE__), '\\/'));

/**
 *  ====================
 *  FUNCIONES
 *  ====================
 */

class Utils
{

    /**
     * Url de la descarga
     * @var string
     */
    public $repoUrl;

    /**
     * Nombre archivo
     * @var string
     */
    public $zipName;

    /**
     * Constructor
     */
    public function __construct(
        string $repoUrl,
        string $zipName
    ) {
        $this->repoUrl = $repoUrl;
        $this->zipName = $zipName;
    }

    /**
     * Plantilla doctype
     *
     * @param string $title - titulo
     * @param string $content - contenido de la página
     *
     * @return void
     */
    public function htmlTemplate(
        string $title,
        string $content
    ): void {
        // salida buffer
        ob_start();
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <title>{$title}</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            </head>
            <body class="position-relative h-100 bg-primary">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 m-auto">
                            <div class="card my-5 h-50 shadow">
                                <div class="card-header p-3 text-primary fw-bold h5">{$title}</div>
                                <div class="card-body">{$content}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            </body>
            </html>
        HTML;
        die($html);
        // inicializa
        ob_flush();
        flush();
        exit();
    }

    /**
     * Descarga archivo
     *
     * @param string $fileUrl - url del repositorio
     * @param string $saveTo - nombre del archivo a guardar
     * @param callable $callback - callback
     *
     * @return void
     */
    public function downloadZipFile(
        string $url,
        string $filepath,
        callable $callback
    ): void {

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            die('Error, se necesita una url valida para la descarga del paquete.');
            exit();
        }

        if (!file_exists($filepath) && !is_file($filepath)) {
            $zipResource = fopen($filepath, "w");
            // Obtenemos archivo zip
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, "self::progressCallback");
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FILE, $zipResource);
            $page = curl_exec($ch);
            if (!$page) {
                echo "Error :- " . curl_error($ch);
            }
            curl_close($ch);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        } else {
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    /**
     * Callback estatica de curl con barra de progreso
     *
     * @param $resource
     * @param int $download_size
     * @param int $downloaded_size
     * @param int $upload_size
     * @param int $uploaded_size
     *
     * @return void
     */
    public static function progressCallback(
        $resource,
        int $download_size,
        int $downloaded_size,
        int $upload_size,
        int $uploaded_size
    ): void {
        static $previousProgress = 0;

        $progress = ($download_size == 0) ? 0 : round($downloaded_size * 100 / $download_size);

        if ($progress > $previousProgress) {
            $previousProgress = $progress;
            $temp_progress = $progress;
        }
        // actualizamos los ids
        echo <<<HTML
            <script>
                document.getElementById("prog").value = "{$progress}";
                document.getElementById("info").textContent = "{$progress}%";
            </script>
        HTML;
        // liberamos
        ob_flush();
        flush();
    }

    /**
     * Barra de progreso
     *
     * @return void
     */
    public function curlProgressBar(): void
    {
        // salida buffer
        ob_start();
        // plantilla de la barra de progreso
        echo <<<HTML
            <div style="display:grid;margin:auto">
                <progress id="prog" value="0" max="100.0"></progress>
                <span id="info"></span>
            </div>
        HTML;
        // inicializa
        ob_flush();
        flush();
    }

    /**
     * Descarga archivo
     *
     * @param string $filename - nombbre archivo a descomprimir
     * @param string $path - directorio donde se extraera
     *
     * @return void
     */
    public function extractZip(
        string $filename,
        string $path,
        callable $callback
    ): void {
        $zip = new ZipArchive();
        $res = $zip->open($filename);
        if ($res === true) {
            // extract it to the path we determined above
            $zip->extractTo($path);
            $zip->close();
            call_user_func($callback);
        } else {
            $this->htmlTemplate("Error", "Lo sentimos, no se ha podido descomprimir $filename");
        }
    }
}

class Scene extends Utils
{
    // Vista de inicio
    public function homeScene()
    {
        // Plantilla de inicio
        $c = <<<HTML
            <p class="card-text">Los grandes <strong>CMS</strong> utilizan <code>MySQL</code> o sistemas similares de gestión de bases de datos en un
            segundo plano. Los sistemas de gestión de bases de datos <strong>(SGBD)</strong> actúan de manera relacional y
            trabajan con varias tablas para gestionar las consultas, para lo que necesitan un servidor adicional. Los
            <strong>flat file CMS</strong> no cuentan con elementos de gestión de bases de datos, por lo que es habitual hablar
            de ellos como <strong>CMS sin bases de datos</strong>. Con ello no hay lugar para los <strong>SGBD</strong> y
            tampoco para los servidores configurados a tales efectos. </p>
            <p class="card-text">Estos sistemas pueden o bien erigirse como la solución perfecta o lograr simplicidad donde se necesita complejidad.
            Las ventajas de los sistemas de gestión de contenidos basados en archivos planos surgen en la mayoría de los casos
            de su estructura simple:</p>
            <p class="card-text">Si desea instalar AntCMS pulse <a href="?view=settings">aquí</a>.</p>
        HTML;
        $this->htmlTemplate('Bienvenido a AntCMS', $c);
    }
    // Vista de info
    public function defaultScene()
    {
        // Plantilla de introdución
        $c = <<<HTML
            <p class="card-text">Hola, si has llegado hasta aquí es por que quieres instalar AntCMS.</p>
            <p class="card-text">Pincha <a href="?view=info">aquí</a> para ir a los ajustes de instalación.</p>
        HTML;
        $this->htmlTemplate('Instalación AntCMS', $c);
    }
    // Vista de proceso descarga
    public function infoScene()
    {
        // Plantilla descarga
        $c = <<<HTML
            <p class="card-text">Vamos a proceder a descargar el archivo en formato <em class="text-primary">.zip</em> que contiene el paquete con el contenido por defecto de AntCMS.</p>
            <p class="card-text">Una vez descargado se procedera a descomprimirlo <strong>(la descarga tárdara unos segundos dependiendo de la conexión)</strong>.</p>
            <div class="alert alert-warning my-2">
                <strong>Nota: </strong>si usted desea hacer la instalación manualmente <a class="link-dark" href="https://monchovarela.es/_proyectos/antmin/antcms.zip" title="Enlace de descarga">aquí</a> tiene el enlace de descarga.
            </div>
            <p><a class="btn btn-sm btn-primary" href="?view=descarga">Descarga</a> </p>
        HTML;
        $this->htmlTemplate('Información', $c);
    }
    // Descarga y callback
    public function downloadScene()
    {
        // enseñamos la barra de progreso
        $this->curlProgressBar();
        // descargamos el archivo y invocamos el callback
        $filename = ROOT_DIR . '/' . $this->zipName;
        $this->downloadZipFile($this->repoUrl, $filename, function () {
            $c = <<<HTML
                <p class="card-text">Ok ya hemos procedido a descargar el archivo ahora procederemos a descomprimirlo y despues instalarlo.</p>
                <p><a class="btn btn-sm btn-primary" href="?view=unzip">Instalar</a> </p>
                <script rel="javascript">
                    // ocultamos la barra de progreso
                    document.getElementById("prog").style.display = 'none';
                    document.getElementById("info").style.display = 'none';
                </script>
            HTML;
            $this->htmlTemplate('Instalación', $c);
        });
    }
    // Callback unzip
    public function unzipSceneCallback()
    {
        $c = <<<HTML
            <p class="card-text">Perfecto ya se hemos descomprimido el archivo y ya puede empezar a editar.</p>
            <p class="card-text">Recuerde que tiene que editar el archivo <code>antcms/config/default.php</code> y cambiar la url por la de la web para que funcione.</p>
            <p class="card-text">Por favor borre el archivo <code>install.php</code> para prevenir que no se vuelva a crear el proceso de instalación.</p>
        HTML;
        $this->htmlTemplate('Descomprimiendo', $c);
    }
    // Unzip
    public function unzipScene()
    {
        // extraemos el archivo y invocamos el callback
        $this->extractZip($this->zipName, ROOT_DIR, function () {
            $this->unzipSceneCallback();
        });
    }
}

/**
 *  ====================
 *  VISTAS: SWITCH
 *  ====================
 */
$scene = new Scene('http://localhost/antdemo/antcms.zip', 'antcms.zip');
if (array_key_exists('view', $_GET)) {
    $view = $_GET['view'];
    switch ($view) {
        case 'unzip':
            $scene->unzipScene();
            break;
        case 'descarga':
            $scene->downloadScene();
            break;
        case 'info':
            $scene->infoScene();
            break;
        default:
            $scene->defaultScene();
            break;
    }
}

$scene->homeScene();
