<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 12:27 PM
 */

namespace Quiz\Models;


use Core\BaseModel;

class Question extends BaseModel
{

    /**
     * Related quiz model
     *
     * @var Quiz
     */
    private $_quiz;

    /**
     * Question text property.
     *
     * @var string|string
     */
    private $_question_text;

    /**
     * related answers property
     *
     * @var array
     */
    private $_answers;

    /**
     * DB relation table and direction
     *
     * @var string
     */
    protected $db_table = 'question';
    protected $db_belongs_to = 'quiz';
    protected $db_has_many = 'answer';

    /**
     * Question constructor.
     *
     * @param Quiz $quiz
     * @param int|null $id
     */
    public function __construct(Quiz $quiz, int $id = null)
    {
        parent::__construct();

        $this->_quiz = $quiz;
        $this->_id = $id;


        $fields = $this->getById($this->_id);

        if (is_array($fields) && !empty($fields)) {
            $this->_question_text = (array_key_exists('question_text', $fields) && !empty($fields['question_text'])) ? $fields['question_text'] : '';
            $this->getAnswers();
        }

    }

    /**
     * Get related answers from DB
     */
    private function getAnswers()
    {
        $this->_answers = [];
        $arrAnswers = $this->getAllRelatedAssoc($this->_id, $this->db_has_many, 'has-many');

        if (!empty($arrAnswers)) {
            foreach ($arrAnswers as $arrAnswer) {
                array_push($this->_answers, new Answer($this, $arrAnswer['id']));
            }
        }
    }

    /**
     * Get/Set ID property
     *
     * @param int|null $value
     *
     * @return int
     */
    public function id(int $value = null)
    {
        if (empty($value))
            return $this->_id;
        else
            $this->_id = filter_var($value, FILTER_SANITIZE_NUMBER_INT);;
    }

    /**
     * Return related quiz model
     *
     * @return Quiz
     */
    public function quiz()
    {
        return $this->_quiz;
    }

    /**
     * Get/Set question property
     *
     * @param string|null $value
     *
     * @return mixed|string
     */
    public function question(string $value = null)
    {
        if (empty($value))
            return $this->_question_text;
        else
            $this->_question_text = filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Return related answers
     *
     * @return mixed
     */
    public function answers()
    {
        return $this->_answers;
    }


    /**
     * Return model
     *
     * @return $this
     */
    public function get()
    {
        return $this;
    }

    /**
     * Save model fata into DB
     *
     * @return bool
     */
    public function save()
    {

        if (empty($this->_question_text)||empty($this->_quiz))
            return false;

        $sql = 'INSERT INTO ' . $this->db_table_prefix . $this->db_table . ' (quiz_id, question_text) VALUES (' . $this->_quiz->id() . ', ' . $this->quoteString($this->_question_text) . ')';

        try {
            $this->beginTransaction();
            $this->exec($sql);
            $this->_id = $this->lastInsertId();
            $this->commitTransaction();
            return true;
        } catch (\PDOException $e) {
            $this->rollbackTransaction();
            $this->_id = null;
            return false;
        }

    }

}