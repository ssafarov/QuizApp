<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/14/19
 * Time: 1:09 PM
 */

namespace Quiz\Controllers;

use Core\AbstractController;
use Core\View;
use Quiz\Repos\QuizManager;
use Quiz\Repos\UserManager;

class TakeQuiz extends AbstractController
{
    private $manager;

    private $localManager;

    /**
     * TakeQuiz constructor.
     *
     * @param array $route_params
     * @throws \Exception
     */
    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        $this->manager = new QuizManager();
    }

    /**
     * Main action. Display the initial page
     *
     * @throws \Exception
     */
    public function indexAction()
    {
        $sectionHeader = "Quiz Application";
        $sectionSubHeader = "Please start with filling form below";

        $availableQuizzes = $this->manager->listAll(true);
        $csrf = $this->getCsrf();

        View::renderTemplate('Take/index.twig', compact('sectionHeader', 'sectionSubHeader', 'csrf', 'availableQuizzes'));
    }


    /**
     * Display new quiz starter
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function startNewAction($params = [])
    {
        $sectionHeader = "Quiz Applicatuon";
        $sectionSubHeader = "Please start with filling form below.";

        $quizStarted = false;
        $csrf = $this->getCsrf();

        $params = empty($params)?$_POST:$params;

        if (isset($params) && array_key_exists('csrf', $params) && $this->checkCsrf($params['csrf'])) {

            $name = array_key_exists('name', $params) && !empty($params['name']) && filter_var(trim($params['name']), FILTER_SANITIZE_STRING) ? filter_var(trim($params['name']), FILTER_SANITIZE_STRING) : false;
            $quiz_id = array_key_exists('quiz_id', $params) && !empty($params['quiz_id']) && filter_var($params['quiz_id'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($params['quiz_id'], FILTER_SANITIZE_NUMBER_INT)) : false;


            $flashMessage = array_key_exists('flash_message', $params) && !empty($params['flash_message']) && filter_var(trim($params['flash_message']), FILTER_SANITIZE_STRING) ? filter_var(trim($params['flash_message']), FILTER_SANITIZE_STRING) : false;
            $flashStatus = array_key_exists('flash_status', $params) && !empty($params['flash_status']) && filter_var(trim($params['flash_status']), FILTER_SANITIZE_STRING) ? filter_var(trim($params['flash_status']), FILTER_SANITIZE_STRING) : false;

            if ($name === false) {

                $flashMessage = 'You have to type your name to start. Please try again';
                $flashStatus = 'danger';
            } elseif ($quiz_id === false) {

                $flashMessage = 'You have to select quiz to start. Please try again';
                $flashStatus = 'danger';
            } else {

                // Set Quiz model first
                $this->manager->setModel($quiz_id);

                // Than create user manager and link it with quiz
                $this->localManager = new UserManager($this->manager->getModel());
                $quizStarted = $this->localManager->setUser($name);

                $flashMessage = !$flashMessage?$this->localManager->getResultMessage():$flashMessage;
                $flashStatus = !$flashStatus?$this->localManager->getResultStatus():$flashStatus;
            }
        } else {

            $flashMessage = 'Something wrong happened. Please try again';
            $flashStatus = 'danger';
        }

        if ($quizStarted) {

            $sectionHeader = 'New quiz started';
            $sectionSubHeader = "Please select an answer and hit \"Submit answer\". Current user registered";

            $user = $this->localManager->getModel();
            $quiz = $this->manager->getModel();
            $step = 1;
            $question = $quiz->questions()[$step - 1];

            View::renderTemplate('Take/quiz.twig', compact('sectionHeader', 'sectionSubHeader', 'csrf', 'flashMessage', 'flashStatus', 'user', 'question', 'step'));
        } else {
            $availableQuizzes = $this->manager->listAll(true);

            View::renderTemplate('Take/index.twig', compact('sectionHeader', 'sectionSubHeader', 'csrf', 'name', 'availableQuizzes', 'flashMessage', 'flashStatus'));
        }
    }


    /**
     * Get quiz next question logick/page
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function getNextQuestionAction($params = [])
    {
        $sectionHeader = "Quiz in Progress";
        $sectionSubHeader = "Please select an answer and hit Next.";

        $quizStarted = false;
        $csrf = $this->getCsrf();
        $params = empty($params)?$_POST:$params;

        if (isset($params) && array_key_exists('csrf', $params) && $this->checkCsrf($params['csrf'])) {

            $name = array_key_exists('name', $params) && !empty($params['name']) && filter_var(trim($params['name']), FILTER_SANITIZE_STRING) ? filter_var(trim($params['name']), FILTER_SANITIZE_STRING) : false;
            $quiz_id = array_key_exists('quiz_id', $params) && !empty($params['quiz_id']) && filter_var($params['quiz_id'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($params['quiz_id'], FILTER_SANITIZE_NUMBER_INT)) : false;
            $step = array_key_exists('step', $params) && !empty($params['step']) && filter_var($params['step'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($params['step'], FILTER_SANITIZE_NUMBER_INT)): 1;

            if ($quiz_id === false) {
                $flashMessage = 'You have to select quiz to start. Please try again';
                $flashStatus = 'danger';
            } else {

                // Set Quiz model first
                $this->manager->setModel($quiz_id);

                // Than create user manager and link it with quiz
                $this->localManager = new UserManager($this->manager->getModel());
                $quizStarted = $this->localManager->setUser($name);

                $flashMessage = $this->localManager->getResultMessage();
                $flashStatus = $this->localManager->getResultStatus();

                if ($quizStarted) {

                    $sectionHeader = "New quiz started";
                    $sectionSubHeader = "Please select an answer and hit \"Submit answer\". Current user registered";

                    $user = $this->localManager->getModel();
                    $quiz = $this->manager->getModel();
                    $nextQuestionPresent = ((count($quiz->questions()) >= $step) && isset($quiz->questions()[$step - 1]));

                    if ($nextQuestionPresent){

                        $question = $quiz->questions()[$step - 1];
                        View::renderTemplate('Take/quiz.twig', compact('sectionHeader', 'sectionSubHeader', 'csrf', 'flashMessage', 'flashStatus', 'user', 'question', 'step'));
                    } else {

                        $sectionHeader = "Quiz finished";
                        $sectionSubHeader = "Here is the results";

                        View::renderTemplate('Take/thankYou.twig', compact('sectionHeader', 'sectionSubHeader', 'user', 'quiz' ));
                    }

                }
            }
        } else {

            $flashMessage = 'Something wrong happened. Please try again';
            $flashStatus = 'danger';

            $availableQuizzes = $this->manager->listAll(true);

            View::renderTemplate('Take/index.twig', compact('sectionHeader', 'sectionSubHeader', 'csrf', 'name', 'availableQuizzes', 'flashMessage', 'flashStatus'));

        }

    }

    /**
     * Save quiz step logic
     *
     * @throws \Exception
     */
    public function saveStepAction()
    {
        $flashMessage = 'Something wrong happened. Please try again';
        $flashStatus = 'danger';

        if (isset($_POST) && array_key_exists('csrf', $_POST) && $this->checkCsrf($_POST['csrf'])) {
            $name = array_key_exists('name', $_POST) && !empty($_POST['name']) && filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING) ? filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING) : false;
            $quiz_id = array_key_exists('quiz_id', $_POST) && !empty($_POST['quiz_id']) && filter_var($_POST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($_POST['quiz_id'], FILTER_SANITIZE_NUMBER_INT)) : false;
            $question_id = array_key_exists('question_id', $_POST) && !empty($_POST['question_id']) && filter_var($_POST['question_id'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($_POST['question_id'], FILTER_SANITIZE_NUMBER_INT)) : false;
            $answer_id = array_key_exists('answer_option', $_POST) && !empty($_POST['answer_option']) && filter_var($_POST['answer_option'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($_POST['answer_option'], FILTER_SANITIZE_NUMBER_INT)) : false;
            $step = array_key_exists('step', $_POST) && !empty($_POST['step']) && filter_var($_POST['step'], FILTER_SANITIZE_NUMBER_INT) ? intval(filter_var($_POST['step'], FILTER_SANITIZE_NUMBER_INT)) : false;


            if ($name === false) {
                $flashMessage = 'You have to type your name to start. Please restart quiz.';
                $flashStatus = 'danger';
            } elseif ($quiz_id === false) {
                $flashMessage = 'You have to select quiz to start. Please restart quiz.';
                $flashStatus = 'danger';
            } elseif ($answer_id === false) {
                $flashMessage = 'You have to select an answer. Please try again';
                $flashStatus = 'warning';

            } else {

                // Set Quiz model first
                $this->manager->setModel($quiz_id);

                // Than create user manager and link it with quiz
                $this->localManager = new UserManager($this->manager->getModel());
                $this->localManager->setUser($name);
                $quizinProgress = $this->localManager->saveUserAnswer($question_id, $answer_id);

                if ($quizinProgress){
                    $step++;
                }

                $flashMessage = $this->localManager->getResultMessage();
                $flashStatus = $this->localManager->getResultStatus();
            }

            $params = ['csrf'=> $_POST['csrf'], 'name'=>$name, 'quiz_id'=>$quiz_id, 'step'=>$step, 'flash_message'=>$flashMessage, 'flash_status'=>$flashStatus];
            $this->getNextQuestionAction($params);
        } else {

            $params = ['csrf'=> $_POST['csrf'], 'flash_message'=>$flashMessage, 'flash_status'=>$flashStatus];
            $this->startNewAction($params);
        }

    }
}