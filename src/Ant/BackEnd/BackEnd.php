<?php

declare (strict_types = 1);

namespace Ant\BackEnd;

defined('ACCESS') or exit('No direct script access allowed');

use Vendor\Session\Session as Session;

final class BackEnd
{

    // Traits
    use Traits\AuthenticationTrait;
    use Traits\FileManagerTrait;
    use Traits\FormFunctionsTrait;
    use Traits\HtmlStructureManagerTrait;
    use Traits\HtmlViewManagerTrait;
    use Traits\MessageHandlerTrait;
    use Traits\RequestHandlerTrait;
    use Traits\RouteHandlerTrait;
    use Traits\SanitizerTrait;
    use Traits\TokenManagerTrait;
    use Traits\UtilitiesTrait;

    /**
     * Lenguaje
     *
     * @var array
     */
    private $__lang = [];

    /**
     * Ip
     *
     * @var string
     */
    private $__ip = "";

    /**
     * Client hash
     *
     * @var string
     */
    private $__client_hash = "";

    /**
     * Login hash
     *
     * @var string
     */
    private $__login_hash = "";

    /**
     * Construct
     *
     * @param array $config
     */
    public function __construct(array $lang = [])
    {
        Session::start();

        $this->__lang   = $lang;

        // Hash de login y cliente
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            $ip = isset($_SERVER[$key]) && !empty($_SERVER[$key]) ? explode(',', $_SERVER[$key])[0] : '';
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                break;
            }
        }
        $this->__ip          = $ip;
        $this->__client_hash = md5($ip . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . __FILE__ . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));

        $this->__login_hash = md5(PASSWORD_HASH . $this->__client_hash);
    }

    public function init()
    {
        $this->routes();
    }
}
