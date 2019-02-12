<?php

namespace Quiz\Controllers;

use Core\AbstractController;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class ControlCenter extends AbstractController
{

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function indexAction()
    {
        $quizesTaken = 5;
        $quizesTotal = 18;

        View::renderTemplate('Backend/index.twig', compact('quizesTaken', 'quizesTotal') );
    }

    public function takenQuizesAction()
    {
        View::renderTemplate('Backend/takenQuizes.twig' );
    }

    public function manageQuizesAction()
    {
        View::renderTemplate('Backend/manageQuizes.twig' );
    }
}
