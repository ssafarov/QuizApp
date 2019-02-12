<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/8/19
 * Time: 1:42 PM
 */

namespace Core;

/**
 * Base controller class
 *
 * Class BaseController
 */
abstract class AbstractController
{
    /**
     * Parameters from the route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
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

}