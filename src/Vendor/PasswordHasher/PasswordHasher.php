<?php

declare (strict_types = 1);

namespace Vendor\PasswordHasher;

defined('ACCESS') or exit('No direct script access allowed');


/**
 * Clase PasswordHasher para el manejo de contraseñas seguras
 *
 * <code>
 *
 *  $hasher = new PasswordHasher(PASSWORD_BCRYPT, ['cost' => 12]);
 *  $test = $hasher->hash('demo');
 *  $hasher->verify('demo', $test)
 *
 * </code>
 *
 */
final class PasswordHasher
{

    /**
     * @var string Algoritmo de hash a utilizar
     */
    private $__hashAlgorithm;

    /**
     * @var array Opciones del algoritmo de hash
     */
    private $__options;

    /**
     * Constructor de la clase PasswordHasher
     *
     * @param string $hashAlgorithm Algoritmo de hash a utilizar
     * @param array $options Opciones del algoritmo de hash
     */
    public function __construct(string $hashAlgorithm = PASSWORD_DEFAULT, array $options = [])
    {
        $this->__hashAlgorithm = $hashAlgorithm;
        $this->__options = $options;
    }

    /**
     * Hash a la contraseña
     *
     * @param string $password Contraseña a hashear
     * @return string Hash resultante
     */
    public function hash(string $password): string
    {
        return password_hash($password, $this->__hashAlgorithm, $this->__options);
    }

    /**
     * Verificación de la contraseña
     *
     * @param string $password Contraseña sin hashear
     * @param string $hash Hash de la contraseña almacenada en la base de datos
     * @return bool Resultado de la verificación
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Comprueba si el hash necesita ser actualizado
     *
     * @param string $hash Hash de la contraseña almacenada en la base de datos
     * @return bool Resultado de la comprobación
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->__hashAlgorithm, $this->__options);
    }
}