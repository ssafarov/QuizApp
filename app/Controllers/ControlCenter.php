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
        $quizzesTaken = 5;
        $quizzesTotal = 18;

        View::renderTemplate('Backend/index.twig', compact('quizzesTaken', 'quizzesTotal') );
    }

    /**
     * Show the Taken Quizzes page
     *
     * @return void
     * @throws \Exception
     */
    public function takenQuizzesAction()
    {
        View::renderTemplate('Backend/takenQuizzes.twig' );
    }

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function manageQuizzesAction()
    {
        View::renderTemplate('Backend/manageQuizzes.twig' );
    }
}
