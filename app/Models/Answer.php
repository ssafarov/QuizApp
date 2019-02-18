<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 12:27 PM
 */

namespace Quiz\Models;


use Core\BaseModel;

class Answer extends BaseModel
{

    /**
     * DB relation table and direction
     *
     * @var string
     */
    protected $db_table = 'answer';
    protected $db_belongs_to = 'question';

    /**
     * Property. Related question
     * @var Question
     */
    private $_question;

    /**
     * Property. Answer text itself.
     *
     * @var string|null
     */
    private $_answerText;

    /**
     * Property. Is this answer right or not
     *
     * @var bool
     */
    private $_isRight;

    /**
     * Answer constructor.
     *
     * @param Question $question
     *
     * @param int|null $id
     */
    public function __construct(Question $question, int $id = null)
    {
        parent::__construct();

        $this->_question = $question;
        $this->_id = $id;

        $fields = $this->getById($this->_id);

        if (is_array($fields) && !empty($fields)) {

            $this->_answerText = (array_key_exists('answer_text', $fields)) ? $fields['answer_text'] : null;
            $this->_isRight = (array_key_exists('answer_right', $fields) && !empty($fields['answer_right'])) ? boolval($fields['answer_right']) : false;

        }

    }

    /**
     * Return this model
     *
     * @return $this
     */
    public function get()
    {
        return $this;
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
            $this->_id = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Get/Set Answer text property
     *
     * @param string|null $value
     *
     * @return mixed|null
     */
    public function answer(string $value = null)
    {
        if (empty($value))
            return $this->_answerText;
        else
            $this->_answerText = filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Get/Set is this answer is right or not
     *
     * @param int|null $value
     *
     * @return bool
     */
    public function isRight(int $value = null)
    {
        if (empty($value) && !is_numeric($value))
            return $this->_isRight;
        else
            $this->_isRight = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Save model data into DB
     *
     * @return bool
     */
    public function save()
    {
        if (empty($this->_answerText) || empty($this->_question))
            return false;

        $idText = $this->checkIfExist('answer_text', $this->_answerText);
        $idQuestion = $this->checkIfExist('question_id', $this->_question->id());

        $this->_id = $idText == $idQuestion ? $idText : null;

        if ($this->_id) {
            $sql = 'UPDATE ' . $this->db_table_prefix . $this->db_table . ' SET question_id, answer_text, answer_right) VALUES (' . $this->_question->id() . ', ' . $this->quoteString($this->_answerText) . ', ' . $this->_isRight . ')';
        } else {
            $sql = 'INSERT INTO ' . $this->db_table_prefix . $this->db_table . ' (question_id, answer_text, answer_right) VALUES (' . $this->_question->id() . ', ' . $this->quoteString($this->_answerText) . ', ' . $this->_isRight . ')';
        }

        try {
            $this->beginTransaction();
            $this->exec($sql);
            $this->_id = $this->_id ?? $this->lastInsertId();
            $this->commitTransaction();
            return true;
        } catch (\PDOException $e) {
            $this->rollbackTransaction();
            $this->_id = null;
            return false;
        }

    }

}