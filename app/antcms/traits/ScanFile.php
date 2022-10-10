<?php

declare (strict_types = 1);

namespace Traits;

defined('SECURE') or die('No tiene acceso al script.');

use RecursiveIteratorIterator;

trait ScanFile
{
    /**
     * Escanear carpeta
     *
     * <code>
     *  AntCMS::scanFiles(CONTENT,'md',false);
     * </code>
     *
     * @param string $folder
     * @param string $type
     * @param bool   $file_path
     *
     * @return array
     */
    public static function scanFiles(
        string $folder,
        string $type = 'html',
        bool $file_path = true
    ): array
    {
        $data = [];
        if (is_dir($folder)) {
            foreach ($iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder,
                    \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $file) {
                if (null !== $type) {
                    if (is_array($type)) {
                        $file_ext = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($file_ext, $type)) {
                            if (strpos($file->getFilename(), $file_ext, 1)) {
                                if ($file_path) {
                                    $data[] = $file->getPathName();
                                } else {
                                    $data[] = $file->getFilename();
                                }
                            }
                        }
                    } else {
                        if (strpos($file->getFilename(), $type, 1)) {
                            if ($file_path) {
                                $data[] = $file->getPathName();
                            } else {
                                $data[] = $file->getFilename();
                            }
                        }
                    }
                } else {
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
            return false;
        }
    }
}
