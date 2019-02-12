<?php

namespace Quiz\Controllers;

use Core\AbstractController;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends AbstractController
{

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function indexAction()
    {
        View::renderTemplate('Home/index.twig');
    }
}
