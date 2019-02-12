<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/8/19
 * Time: 2:09 PM
 */

namespace Core;


use Quiz\Config;

class View
{
    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws \Exception
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/Views/$view";

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("File '".$file."' not found for render.");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws \Exception
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        $args = array_merge(['title'=> Config::APP_TITLE], $args);

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/Templates');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }
}