<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 12:33 PM
 */

namespace Quiz\Repos;

use Quiz\Models\Answer;
use Quiz\Models\Question;
use Quiz\Models\Quiz;

class QuizManager
{
    /**
     * Working primary model
     *
     * @var Quiz
     */
    private $model;

    /**
     * Working secondry models
     * @var array
     */
    private $quizzes;

    /**
     * Flash messages status
     * @var string
     */
    private $resultStatus;

    /**
     * Flash messages text
     * @var string
     */
    private $resultMessage;


    /**
     * QuizManager constructor.
     *
     * @param int|null $quiz_id
     */
    public function __construct(int $quiz_id = null)
    {
        $this->model = new Quiz($quiz_id);
        $this->quizzes = [];
    }

    /**
     *  Get status of the last operation for the flash messages
     *
     * @return mixed
     */
    public function getResultStatus()
    {
        return $this->resultStatus;
    }

    /**
     *  Get message of the last operation for the flash messages
     *
     * @return mixed
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * Return the total quizzzes amount registered in the system
     *
     * @return int
     */
    public function getTotalQuizAmount()
    {
        return $this->model->getTotalAmount();
    }

    /**
     * Set Quiz model to work with
     *
     * @param int $quiz_id
     */
    public function setModel(int $quiz_id)
    {
        $this->model->id($quiz_id);
        $this->model = $this->model->get();
    }

    /**
     * Get the selected/created model
     *
     * @return Quiz
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the model by it's ID from DB
     *
     * @param $id
     * @return array|null
     */
    public function getByID($id)
    {
        return $this->model->getById($id);
    }

    /**
     * List all models from DB
     *
     * @param bool $nonEmpty
     * @return array
     */
    public function listAll($nonEmpty = false)
    {

        $models = $this->model->getAll('title');

        foreach ($models as $model){
            $quiz = new Quiz($model['id']);
            if (count($quiz->questions()) || !$nonEmpty){
                array_push($this->quizzes, $quiz);
            }

        }

        return $this->quizzes;
    }

    /**
     * Save quiz logic
     *
     * @param array $params
     *
     * @return bool
     */
    public function saveQuiz($params = [])
    {
        $result = false;
        $this->resultStatus='danger';
        $this->resultMessage='Unable to save quiz';


        $id = array_key_exists('id', $params)?intval(filter_var($params['id'], FILTER_SANITIZE_NUMBER_INT)):null;
        $title = array_key_exists('title', $params)?trim(filter_var($params['description'], FILTER_SANITIZE_STRING)):null;
        $description = array_key_exists('description', $params)?trim(filter_var($params['description'], FILTER_SANITIZE_STRING)):null;

        if ($title){
            $this->model->id($id);
            $this->model->title(trim(filter_var($params['title'], FILTER_SANITIZE_STRING)));
            $this->model->description($description);

            $result = $this->model->save();

            if ($result){
                $this->resultStatus='info';
                $this->resultMessage='Quiz was saved successfully';
            } else {
                $this->resultStatus='warning';
                $this->resultMessage='Quiz was not saved';
            }

        } else {
            $this->resultStatus='danger';
            $this->resultMessage='You have to set Quiz title to save it';
        }

        return $result;
    }

    /**
     * Save question into DB
     *
     * @param array $params
     *
     * @return bool
     */
    public function saveQuestion($params = [])
    {
        $result = false;
        $this->resultStatus='danger';
        $this->resultMessage='Unable to save question';

        $quiz_id = !empty($params['quiz'])?intval(filter_var($params['quiz'], FILTER_SANITIZE_NUMBER_INT)):false;
        $questionText = !empty($params['question'])?filter_var(trim($params['question']), FILTER_SANITIZE_STRING):false;
        $answers = is_array($params['answer'])&&!empty($params['answer'])?$params['answer']:false;

        $answerRightIndex = !empty($params['answer_right'])?intval(filter_var($params['answer_right'], FILTER_SANITIZE_NUMBER_INT)):false;

        if ($quiz_id && $questionText && $answers && $answerRightIndex) {
            $this->model->id($quiz_id);
            $question = new Question($this->model->get());
            $question->question($questionText);
            $result = $question->save();

            foreach ($answers as $index=>$answer) {
                $objAnswer = new Answer($question);
                $objAnswer->answer(filter_var(trim($answer), FILTER_SANITIZE_STRING));
                $objAnswer->isRight($answerRightIndex == $index ? 1:null);
                $result = $objAnswer->save() && $result;
            }

            $this->resultStatus = 'info';
            $this->resultMessage = 'Question was saved successfully';

        } else if (!$quiz_id) {
            $this->resultStatus='danger';
            $this->resultMessage='You have to choose Quiz to save the question';
        } else if (!$questionText){
            $this->resultStatus='danger';
            $this->resultMessage='You have to type question';
        } else if (!$answers){
            $this->resultStatus='danger';
            $this->resultMessage='You have to type at least to answers';
        } else if (!$answerRightIndex){
            $this->resultStatus='danger';
            $this->resultMessage='You have to select a right answer';
        } else if (!$result){
            $this->resultStatus='danger';
            $this->resultMessage='There\'re an errors during answers save';
        } else {
            $this->resultStatus='danger';
            $this->resultMessage='Question cannot be saved now. Please try again';
        }

        return $result;
    }

    /**
     * DElete quiz and all related questions and answers
     *
     * @param int $quiz_id
     *
     * @return bool
     */
    public function deleteQuiz(int $quiz_id)
    {
        $this->model->id($quiz_id);
        $this->model->get();
        $result = true;

        try {
            $this->model->beginTransaction();
            if ( $this->model->questions() )
            foreach ($this->model->questions() as $question){
                if ($question && $question->answers())
                foreach ($question->answers() as $answer){
                    if ($answer)
                        $result = $answer->delete() && $result;
                }
            }

            $result = $this->model->delete() && $result;

        } catch (\PDOException $e) {
            $result = false;
            $this->model->rollbackTransaction();
        }

        if ( $result ) {
            if ($this->model->isInTransaction())
                $this->model->commitTransaction();

            $this->resultStatus='info';
            $this->resultMessage='Quiz ' . $this->model->title() . ' has been deleted successfully';
        } else {
            if ($this->model->isInTransaction())
                $this->model->rollbackTransaction();

            $this->resultStatus='danger';
            $this->resultMessage='Question ' . $this->model->title() . ' cannot be deleted now. Please try again';
        }

        return $result;
    }
}