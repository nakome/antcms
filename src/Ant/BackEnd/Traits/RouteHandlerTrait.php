<?php

declare (strict_types = 1);

namespace Ant\BackEnd\Traits;

defined('ACCESS') or exit('No direct script access allowed');

/**
 * Trait RouteHandler - Rutas
 * -----------------------------
 * routes: rutas de la aplicación web
 * ------------------------------
 */
trait RouteHandlerTrait
{

    /**
     * Obtener ruta de get y mostrar vista
     *
     * @param array $args
     * @return void
     */
    public function getRoutes(array $args = [])
    {
        if ($this->isLoggedIn()) {
            if (array_key_exists('name', $args)) {
                $route = PUBLIC_ROOT . base64_decode($args['name']);
                if (in_array('file', $args) && is_file($route)) {
                    echo $this->displayPlainView($route);
                    return;
                } elseif (in_array('dir', $args) && is_dir($route)) {
                    echo $this->displayDefaultView($route);
                    return;
                } elseif (in_array('utilities', $args)) {
                    echo $this->displayUtilitiesView();
                    return;
                } elseif (in_array('help', $args)) {
                    echo $this->displayHelpView();
                    return;
                }
            }
        }
        // Si nada se cumple devolvemos error
        die($this->displayErrorLayout());
    }

    /**
     * Obtener ruta de edit y mostrar vista
     *
     * @param array $args
     * @return void
     */
    public function editRoutes(array $args = [])
    {
        if ($this->isLoggedIn()) {
            if (array_key_exists('name', $args)) {
                $route = PUBLIC_ROOT . base64_decode($args['name']);
                if (is_file($route)) {
                    echo $this->displayEditView($route);
                    return;
                }
            }
        }
        // Si nada se cumple devolvemos error
        die($this->displayErrorLayout());
    }

    /**
     * Obtener ruta de rename y mostrar vista
     *
     * @param array $args
     * @return void
     */
    public function renameRoutes(array $args = [])
    {
        if ($this->isLoggedIn()) {
            if (array_key_exists('name', $args)) {
                $route = PUBLIC_ROOT . base64_decode($args['name']);
                if (is_file($route)) {
                    echo $this->displayRenameView($route);
                    return;
                }
            }
        }
        // Si nada se cumple devolvemos error
        die($this->displayErrorLayout());
    }

    /**
     * Obtener ruta de delete y mostrar vista
     *
     * @param array $args
     * @return void
     */
    public function deleteRoutes(array $args = [])
    {
        if ($this->isLoggedIn()) {
            if (array_key_exists('name', $args)) {
                $route = PUBLIC_ROOT . base64_decode($args['name']);
                if (is_file($route) || is_dir($route)) {
                    echo $this->displayDeleteView($route);
                    return;
                }
            }
        }
        // Si nada se cumple devolvemos error
        die($this->displayErrorLayout());
    }

    /**
     * Rutas de la aplicación web y sus controladores
     *
     * @return void
     */
    public function routes()
    {
        // Obtenemos los archivos
        if (array_key_exists('get', $_GET)) {
            $this->getRoutes($_GET);
        } elseif (array_key_exists('edit', $_GET)) {
            $this->editRoutes($_GET);
        } elseif (array_key_exists('rename', $_GET)) {
            $this->renameRoutes($_GET);
        } else if (array_key_exists('delete', $_GET)) {
            $this->deleteRoutes($_GET);
        } else {
            echo $this->isLoggedIn() ? $this->displayDefaultView(PUBLIC_ROOT . '/public/content') : $this->displayLoginView();
        }

        // Salir de la aplicación
        if (array_key_exists('logout', $_GET)) {
            $this->logout();
        }
    }
}
