<?php

declare (strict_types = 1);

namespace AntTpl;

use Traits\Minify as Minify;

defined('SECURE') or die('No tiene acceso al script.');

// Interface 
require_once __DIR__ . '/IAntTPL.php';

// Traits
require_once __DIR__ . '/traits/Minify.php';

/**
 * Ant Template.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 1.0.0
 */
class AntTPL implements IAntTPL
{
    use Minify;

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
    public function set(string $name, $value):object
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
     * @param string $content the content
     *
     * @return string
     */
    private function __parse(
        string $content
    ): string {
        // replace tags with PHP
        foreach ($this->tags as $regexp => $replace) {
            if (false !== strpos($replace, 'self')) {
                $content = preg_replace_callback('#' . $regexp . '#s', $replace, $content);
            } else {
                $content = preg_replace('#' . $regexp . '#', $replace, $content);
            }
        }

        // replace variables
        if (preg_match_all('/(\$(?:[a-zA-Z0-9_-]+)(?:\.(?:(?:[a-zA-Z0-9_-][^\s]+)))*)/', $content, $matches)) {
            for ($i = 0; $i < count($matches[1]); ++$i) {
                // $a.b to $a["b"]
                $rep = $this->__replaceVariable($matches[1][$i]);
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }

        // remove spaces betweend %% and $
        $content = preg_replace('/\%\%\s+/', '%%', $content);

        // call cv() for signed variables
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
     * Run file.
     *
     * @param string $file    the file
     * @param int    $counter the counter
     *
     * @return string
     */
    private function __run(
        string $file,
        int $counter = 0
    ): string {
        $pathInfo = pathinfo($file);
        $tmpFile = $this->tmp . $pathInfo['basename'];

        if (!is_file($file)) {
            echo "Template '$file' not found.";
        } else {
            $content = file_get_contents($file);

            if ($this->__searchTags($content) && ($counter < 3)) {
                file_put_contents($tmpFile, $content);
                $content = $this->__run($tmpFile, ++$counter);
            }
            file_put_contents($tmpFile, $this->__parse($content));

            extract($this->data, EXTR_SKIP);

            ob_start();
            include $tmpFile;
            if (!DEBUG) {
                unlink($tmpFile);
            }
            return ob_get_clean();
        }
    }

    /**
     * Draw file.
     *
     * @param string $file the file
     *
     * @return string
     */
    public function draw(
        string $file
    ): string {
        $result = $this->__run($file);
        return $this->minify_html($result);
    }

    /**
     *  Comment.
     *
     * @param string $content the content
     *
     * @return string
     */
    public function comment(
        string $content
    ): string {
        return null;
    }

    /**
     *  Search Tags.
     *
     * @param string $content the content
     *
     * @return bool
     */
    private function __searchTags(
        string $content
    ): bool {
        foreach ($this->tags as $regexp => $replace) {
            if (preg_match('#' . $regexp . '#sU', $content, $matches)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dot notation.
     *
     * @param string $var the var
     *
     * @return string
     */
    private function __replaceVariable(
        string $var
    ): string {
        if (false === strpos($var, '.')) {
            return $var;
        }

        return preg_replace('/\.([a-zA-Z\-_0-9]*(?![a-zA-Z\-_0-9]*(\'|\")))/', "['$1']", $var);
    }
}
