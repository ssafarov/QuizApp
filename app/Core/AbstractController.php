<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/8/19
 * Time: 1:42 PM
 */

namespace Core;

use GuzzleHttp;

/**
 * Base controller class
 *
 * Class BaseController
 */
abstract class AbstractController
{
    /**
     * session token
     *
     * @var string|null
     */
    protected $csrf_token = null;

    /**
     * Parameters from the route
     *
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params Parameters from the route
     *
     * @return void
     *
     * @throws \Exception
     */
    public function __construct($route_params)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            $this->setCsrf();
        }

        $this->http_request = new GuzzleHttp\Client();

        $this->route_params = $route_params;
    }

    /**
     * Set CSRF Token value for session
     *
     * @throws \Exception
     */
    protected function setCsrf()
    {

        $this->csrf_token = bin2hex(random_bytes(32));

        if (session_status() === PHP_SESSION_ACTIVE) {
            if (!empty($_SESSION['csrf'])) {
                $this->csrf_token = $_SESSION['csrf'];
            } else {
                $_SESSION['csrf'] = $this->csrf_token;
            }
        }

    }

    /**
     * Action methods in the controllers need to be name with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new \Exception("Requested method <" . $method . "> not found in the controller <" . get_class($this) . ">");
        }
    }

    /**
     * Check the actual CSRF token with stored for session
     *
     * @param $token
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function checkCsrf($token)
    {
        return hash_equals($this->getCsrf(), $token);
    }

    /**
     * Get the session CSRF token
     *
     * @return null
     *
     * @throws \Exception
     */
    protected function getCsrf()
    {
        if (empty($_SESSION['csrf'])) {
            $this->setCsrf();
        }

        $this->csrf_token = $_SESSION['csrf'] ? $_SESSION['csrf'] : null;

        return $this->csrf_token;
    }

}