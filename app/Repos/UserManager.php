<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/14/19
 * Time: 1:58 PM
 */

namespace Quiz\Repos;


use Quiz\Models\Answer;
use Quiz\Models\Question;
use Quiz\Models\Quiz;
use Quiz\Models\User;

class UserManager
{
    /**
     * Working second model
     *
     * @var Quiz
     */
    private $quiz;

    /**
     * Working third model
     *
     * @var Quiz
     */
    private $users;

    /**
     * Working primary model
     *
     * @var Quiz
     */
    private $model;

    /**
     * Flash messages text
     * @var string
     */
    private $resultMessage;

    /**
     * Flash messages status
     * @var string
     */
    private $resultStatus;


    /**
     * UserManager constructor.
     *
     * @param Quiz $quiz
     *
     * @param User|null $user
     */
    public function __construct(Quiz $quiz, User $user = null)
    {
        $this->users = [];
        $this->quiz = $quiz;
        $this->model = $user ? $user : new User($this->quiz);
    }

    /**
     * Get user model
     *
     * @return null|User
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get message of the last operation for the flash messages
     *
     * @return null|string
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * Get status of the last operation for the flash messages
     *
     * @return null|string
     */
    public function getResultStatus()
    {
        return $this->resultStatus;
    }

    /**
     * List all model-related records
     *
     * @return array
     */
    public function listAll()
    {

        $models = $this->model->getAll('u_name');

        foreach ($models as $model){
            $quiz = new Quiz($model['quiz_id']);
            $user = new User( $quiz, $model['id'] );
            array_push($this->users, $user);

        }

        return $this->users;
    }

    /**
     * Set user model
     *
     * @param string $name
     *
     * @return bool
     */
    public function setUser(string $name)
    {
        $this->model->name($name);
        $this->model = $this->model->get();
        $saved = $this->model->save();

        $this->resultStatus = $saved ? "info" : "danger";
        $this->resultMessage = $saved? "Saved successfully": "Something bad happened in our database during save, please try again";

        return $saved;
    }

    /**
     * SAve user's answer in the DB
     *
     * @param int $question_id
     * @param int $answer_id
     *
     * @return bool
     */
    public function saveUserAnswer(int $question_id, int $answer_id)
    {
        $localQuestion = new Question($this->quiz,$question_id);
        $localAnswer = new Answer($localQuestion, $answer_id);

        if ($localAnswer->isRight()){
            $rightAnswersSoFar = $this->model->correctAnswers();
            $rightAnswersSoFar++;
            $this->model->correctAnswers($rightAnswersSoFar);
        }

        $saved = $this->model->save();

        $this->resultStatus = $saved ? "info" : "warning";
        $this->resultMessage = $saved? "Saved successfully": "Something bad happened in our database during save, please try again";

        return $saved;
    }

    /**
     * Return total amount of quizzes taken by user
     *
     * @return int
     */
    public function getTotalQuizzesTaken()
    {
        return $this->model->getTotalAmount();
    }

}