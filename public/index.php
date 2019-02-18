<?php

/**
 * Front controller & input point
 *
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Handlers::errorHandler');
set_exception_handler('Core\Handlers::exceptionHandler');


/**
 * Routing
 *
 * Create new router instance
 *
 */
$router = new Core\Router();

/**
 * And add the routes to the app routes 'table'
 *
 */

/**
 * Default Frontend route
 */
$router->add('',  ['controller' => 'Home', 'action' => 'index']);

/**
 * And all other routes
 */
$router->add('{controller}/{action}');
$router->add('{controller}');

/**
 * Parse & run
 */
$router->dispatch($_SERVER['QUERY_STRING']);
