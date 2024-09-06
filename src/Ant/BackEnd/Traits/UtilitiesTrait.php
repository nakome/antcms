<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait Utilities - Utilidades
 * -----------------------------
 * lang: funcion para obtener el texto de una dirección localizada.
 * formatFilesize: funcion para formatear el tamaño de un archivo.
 * getDesktopIp: funcion para obtener la dirección IP local del equipo.
 * isLocalhost: funcion que comprueba si la solicitud se está realizando desde un entorno localhost.
 * redirect: funcion para redirigir a una página.
 * cleanName: funcion para limpiar el nombre de un texto.
 * createDir: funcion para crear un directorio.
 * createFile: funcion para crear un archivo.
 * isFileEditable: función que comprueba si el archivo es editable.
 * createRealLink: funcion para crear un enlace real basado en la ruta de archivo proporcionada.
 * ------------------------------
 */
trait UtilitiesTrait
{

    /**
     * Retorna el valor de una opción específica del array de configuración de la clase.
     *
     * @param string $key La clave de la opción que se desea obtener.
     * @return mixed|null El valor de la función correspondiente a la clave especificada o null si la clave no existe.
     */
    public function lang(string $key): string
    {
        return isset($this->__lang[$key]) ? $this->__lang[$key] : "-----";
    }

    /**
     * Convierte un tamaño de archivo en Bytes a una unidad de medida más legible para el usuario, como KB, MB, GB o TB.
     *
     * @param int $size Tamaño del archivo en Bytes.
     * @return string Tamaño del archivo con la unidad de medida correspondiente.
     */
    public function formatFileSize($size)
    {
        // Array de unidades de medida
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB');

        // Calcula la potencia de la base 1024 necesaria para obtener la unidad de medida correcta
        // Utiliza un operador ternario para verificar si el tamaño del archivo es mayor a cero
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        // Divide el tamaño del archivo por la cantidad resultante de 1024 elevado a la potencia obtenida para obtener el tamaño en la unidad de medida correcta
        $result = $size / pow(1024, $power);

        // Formatea el resultado con dos decimales si no es un número entero
        $formattedResult = is_int($result) ? number_format($result) : number_format($result, 2, '.', ',');

        // Concatena el resultado de la división y la unidad de medida correspondiente, obtenida del array de unidades utilizando el valor de la variable $power como índice
        return $formattedResult . ' ' . $units[$power];
    }

    /**
     * Función para obtener la dirección IP local del equipo.
     *
     * @return string La dirección IP local del equipo.
     */
    public function getDesktopIp(): string
    {
        $localIP = "";
        // Comprobar si la extensión de sockets está cargada en PHP.
        if (extension_loaded('sockets')) {

            // Crear un socket para obtener la dirección IP local del socket.
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

            socket_connect($socket, '8.8.8.8', 53); // Conectar el socket a cualquier dirección IP externa y puerto.
            socket_getsockname($socket, $localIP); // Obtener la dirección IP local del socket.
            socket_close($socket); // Cerrar el socket.

        }
        // Devolver la dirección IP local del equipo.
        return ($this->isLocalhost()) ? $localIP : "";
    }

    /**
     * Función que comprueba si la solicitud se está realizando desde un entorno localhost.
     * @return bool Devuelve true si la solicitud se realiza desde localhost, de lo contrario, false.
     */
    public function isLocalhost()
    {
        $is_localhost = false;

        // Comprobar si la dirección IP comienza con "127.0.0." o si el host es "localhost"
        if (strpos($_SERVER['REMOTE_ADDR'], '127.0.0.') === 0 || $_SERVER['HTTP_HOST'] === 'localhost') {
            $is_localhost = true;
        }

        return ($is_localhost) ? true : false;
    }

    /**
     * Redirecciona a una URL.
     *
     * @param string $url  La URL a la que se redireccionará.
     * @param int $st      El código de estado HTTP a utilizar (por defecto, 302).
     * @param int $wait    El tiempo de espera antes de redireccionar (en segundos).
     */
    public function redirect($url, $st = 302, $wait = 0)
    {
        // Convertir $url y $st a tipos de datos apropiados
        $url = (string) $url;
        $st  = (int) $st;

        // Definir mensajes para los códigos de estado HTTP
        $msg = [
            301 => '301 ' . $this->lang('pageMoved'),
            302 => '302 ' . $this->lang('pageFound'),
        ];

        // Verificar si las cabeceras ya han sido enviadas
        if (headers_sent()) {
            // Si las cabeceras ya han sido enviadas, redireccionar mediante JavaScript
            echo "<script nonce='".NONCE."'>document.location.href='" . $url . "';</script>\n";
        } else {
            // Si las cabeceras no han sido enviadas, configurar la cabecera HTTP y redireccionar mediante PHP
            header('HTTP/1.1 ' . $st . ' ' . ($msg[$st] ?? '302 Found'));
            if ($wait > 0) {
                sleep($wait);
            }
            header("Location: {$url}");
            exit(0);
        }
    }

    /**
     * Elimina caracteres especiales y acentos de un texto
     *
     * @param string $texto Texto a limpiar
     * @return string Texto limpio
     */
    public function cleanName(string $txt): string
    {
        $txt = strtolower($txt); // Convierte el texto a minúsculas
        $txt = str_replace(" ", "-", $txt); // Reemplaza los espacios por guiones
        $txt = preg_replace("/[^a-z0-9-]+/", "", $txt); // Elimina caracteres especiales y acentos
        $txt = trim($txt, "-"); // Elimina guiones al principio y al final
        $txt = preg_replace("/-{2,}/", "-", $txt); // Elimina guiones duplicados

        // Asegurarse de que la cadena no sea demasiado larga
        $max_length = 50;
        if (strlen($txt) > $max_length) {
            $txt = substr($txt, 0, $max_length);
        }
        return $txt;
    }

    /**
     * Comprobamos si el archivo es editable
     *
     * @param string $filePath
     * @return boolean
     */
    private function isFileEditable(string $filePath): bool
    {
        // Lógica para determinar si el archivo es editable (puedes personalizar según tus necesidades)
        // Por ejemplo, puedes verificar la extensión del archivo o permisos de escritura.
        // Aquí hay un ejemplo simple que verifica si la extensión es 'txt':
        $editableExtensions = ['txt', 'php', 'html', 'css', 'js', 'ts', 'scss', 'less', 'json', 'xml', 'yaml', 'scss', 'python', 'php'];
        $fileExtension      = pathinfo($filePath, PATHINFO_EXTENSION);
        return in_array($fileExtension, $editableExtensions);
    }

    /**
     * Crea un enlace real basado en la ruta de archivo proporcionada.
     *
     * @param string $filePath La ruta del archivo para crear el enlace real para
     * @return string
     */
    public function createRealLink(string $filePath): string
    {
        $publicUrl         = str_replace(ADMIN_FILE_NAME, '', ADMIN_URL);
        $sanitizedFilePath = str_replace('//', '', $filePath);
        $relativeFilePath  = str_replace(PUBLIC_ROOT, '', $sanitizedFilePath);
        $relativeFilePath  = ltrim($relativeFilePath, '/');
        $realLink          = $publicUrl . $relativeFilePath;
        if (preg_match('/content/', $realLink)) {
            $realLink = str_replace('public/content/', '', $realLink);
            $realLink = str_replace('.html', '', $realLink);
            if (preg_match('/index/', $realLink)) {
                $realLink = str_replace('index', '', $realLink);
            }
        }
        return $realLink;
    }

    /**
     * Crear un password seguro
     *
     * @param integer $long
     * @return string
     */
    public function generateRandomPassword($long = 12): string
    {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num   = '0123456789';
        $char  = '!@#$%^&*()-_';

        $letters        = $lower . $upper . $num . $char;
        $pass           = '';
        $letters_length = strlen($letters) - 1;

        // Agregar al menos un carácter de cada tipo
        $pass .= $lower[rand(0, strlen($lower) - 1)];
        $pass .= $upper[rand(0, strlen($upper) - 1)];
        $pass .= $num[rand(0, strlen($num) - 1)];
        $pass .= $char[rand(0, strlen($char) - 1)];

        // Generar el resto de la contraseña
        for ($i = 4; $i < $long; $i++) {
            $index = rand(0, $letters_length);
            $pass .= $letters[$index];
        }
        // Mezclar la contraseña para garantizar que los caracteres especiales no estén agrupados
        $pass = str_shuffle($pass);
        return $pass;
    }
}
