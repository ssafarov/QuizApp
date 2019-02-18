<?php

namespace Quiz\Controllers;

use Core\AbstractController;
use Core\View;
use Quiz\Models\Quiz;
use Quiz\Repos\QuizManager;
use Quiz\Repos\UserManager;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class ControlCenter extends AbstractController
{
    private $manager;

    private $localManager;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);
        $this->manager = new QuizManager();
        $this->localManager = new UserManager($this->manager->getModel());
    }

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function indexAction()
    {
        $sectionTitle = 'Control Center';
        $quizzesTaken = $this->localManager->getTotalQuizzesTaken();
        $quizzesTotal = $this->manager->getTotalQuizAmount();

        View::renderTemplate('Backend/index.twig', compact('quizzesTaken', 'quizzesTotal', 'sectionTitle') );
    }

    /**
     * Show the Taken Quizzes page
     *
     * @return void
     * @throws \Exception
     */
    public function takenQuizzesAction()
    {
        $sectionTitle = 'Quizzes taken';
        $taken = $this->localManager->listAll();

        View::renderTemplate('Backend/takenQuizzes.twig', compact('sectionTitle', 'taken') );
    }

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function manageQuizzesAction()
    {
        $sectionTitle = 'Quizzes management';
        $quizzes = $this->manager->listAll();

        View::renderTemplate('Backend/manageQuizzes.twig', compact('quizzes', 'sectionTitle' ) );
    }

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function addNewQuizAction()
    {
        $sectionTitle = 'New quiz';
        $csrf = $this->getCsrf();

        View::renderTemplate('Backend/manageSingleQuiz.twig', compact('sectionTitle', 'csrf') );
    }


    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function editQuizAction()
    {
        $quiz = isset($_GET['id'])?intval(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT)):false;

        if ($quiz) {
            $this->manager->setModel($quiz);
            $quiz = $this->manager->getModel();
            $sectionTitle = "Edit quiz";
            $sectionSubTitle = "Editing quiz";
            $buttonCaption = "Save quiz";

            $csrf = $this->getCsrf();
            View::renderTemplate('Backend/manageSingleQuiz.twig', compact('sectionTitle',  'sectionSubTitle', 'csrf', 'quiz', 'buttonCaption') );
        } else {
            $this->indexAction();
        }

    }

    /**
     * Show the index page
     *
     * @return void
     * @throws \Exception
     */
    public function deleteQuizAction()
    {
        $sectionTitle = 'Quizzes management';

        $quiz_id = isset($_GET['id'])?intval(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT)):false;

        if ($quiz_id ) {

            $this->manager->deleteQuiz($quiz_id);

            $flashStatus = $this->manager->getResultStatus();
            $flashMessage = $this->manager->getResultMessage();

        } else {

            $flashStatus = 'danger';
            $flashMessage = 'You have to select quiz to delete';

        }

        $quizzes = $this->manager->listAll();

        View::renderTemplate('Backend/manageQuizzes.twig', compact('quizzes', 'sectionTitle', 'flashStatus', 'flashMessage' ) );

    }

    /**
     * Save quiz data
     *
     * @return void
     * @throws \Exception
     */
    public function saveQuizAction()
    {
        $params = $_POST;
        $saved = false;

        if ($params && $this->checkCsrf($params['csrf'])) {
            $saved = $this->manager->saveQuiz($params);
        }

        $quiz = $this->manager->getModel();
        $csrf = $this->getCsrf();

        if ($saved){
            $sectionTitle = $quiz->title();
            $params['quiz'] = $quiz->id();
            View::renderTemplate('Backend/manageSingleQuizQuestion.twig', compact('sectionTitle', 'csrf', 'quiz', 'flashStatus', 'flashMessage', 'params') );
        } else {
            $sectionTitle = 'Error with quiz manipulation, please try again';
            View::renderTemplate('Backend/manageSingleQuiz.twig', compact('sectionTitle', 'csrf', 'quiz') );
        }

    }

    /**
     * Save question data
     *
     * @throws \Exception
     */
    public function saveQuestionAction()
    {

        $params = $_POST;

        $sectionTitle = "Edit quiz";

        if ($params && $this->checkCsrf($params['csrf']) && !empty($params['question']) && !empty($params['answer_right'])) {
            $this->manager->saveQuestion($params);
            $flashStatus = $this->manager->getResultStatus();
            $flashMessage = $this->manager->getResultMessage();
        } else if ( empty($params['quiz']) ) {
            $flashStatus = 'danger';
            $flashMessage = 'You have to select a quiz first';
        } else if ( empty($params['question']) ) {
            $flashStatus = 'danger';
            $flashMessage = 'You have to write question';
        } else if ( empty($params['answer_right']) ) {
            $flashStatus = 'danger';
            $flashMessage = 'You have to select a right answer';
        } else {
            $flashStatus = 'danger';
            $flashMessage = 'There is an error during question save. Please try again';
        }

        $csrf = $this->getCsrf();
        $quiz = $this->manager->getModel();

        View::renderTemplate('Backend/manageSingleQuizQuestion.twig', compact('sectionTitle', 'csrf', 'quiz', 'flashStatus', 'flashMessage', 'params') );

    }

}
