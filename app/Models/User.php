<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 12:27 PM
 */

namespace Quiz\Models;


use Core\BaseModel;

class User extends BaseModel
{

    private $_name;
    private $_quiz;

    private $_correctAmount = 0;

    protected $db_table = 'user';


    /**
     * Refresh model based on DB info
     *
     * @return void
     */
    private function _refresh()
    {
        $this->_id = $this->_id??$this->checkIfExist('u_name', $this->_name);
        $fields = $this->getById($this->_id);
        if (is_array($fields) && !empty($fields)) {
            $this->_name  = array_key_exists('u_name', $fields) ? $fields['u_name'] : 'Name not set';
            $this->_correctAmount  = array_key_exists('correct_answers', $fields) ? $fields['correct_answers'] : 0;
        }
    }

    /**
     * User constructor.
     *
     * @param Quiz $quiz
     *
     * @param int|null $id
     */
    public function __construct(Quiz $quiz, int $id = null)
    {
        parent::__construct();

        $this->_id = $id;
        $this->_quiz = $quiz;

        $this->_refresh();

    }

    /**
     * Get model from DB
     *
     * @return $this
     */
    public function get()
    {
        $this->_refresh();
        return $this;
    }

    /**
     * Get/Set model ID property
     *
     * @param int|null $value
     *
     * @return int|null
     */
    public function id(int $value = null)
    {
        if (empty($value))
            return $this->_id;
        else
            $this->_id = $value;
    }

    /**
     * Get/Set model name
     *
     * @param string|null $value
     *
     * @return mixed
     */
    public function name(string $value = null)
    {
        if (empty($value))
            return $this->_name;
        else
            $this->_name = filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Return quiz related
     *
     * @return Quiz
     */
    public function quiz()
    {
        return $this->_quiz;
    }

    /**
     * Return correct answers amount of the related quiz
     *
     * @param int|null $value
     *
     * @return int
     */
    public function correctAnswers(int $value = null)
    {
        if (empty($value))
            return $this->_correctAmount;
        else
            $this->_correctAmount = $value;
    }

    /**
     * Save model data into DB
     *
     * @return bool
     */
    public function save()
    {
        if (empty($this->_name)||empty($this->_quiz))
            return false;

        $this->_id = $this->checkIfExist('u_name', $this->_name);


        if ($this->_id) {
            $sql = 'UPDATE ' . $this->db_table_prefix . $this->db_table . ' SET u_name = ' . $this->quoteString($this->_name) . ', quiz_id = ' . $this->quoteString($this->_quiz->id()) . ', correct_answers = ' . $this->quoteString($this->_correctAmount) . ' WHERE id = ' . $this->quoteString($this->_id);
        } else {
            $sql = 'INSERT INTO ' . $this->db_table_prefix . $this->db_table . ' (u_name, quiz_id, correct_answers) VALUES (' . $this->quoteString($this->_name) . ', ' . $this->_quiz->id() . ', ' . $this->_correctAmount . ')';
        }

        try {
            $this->beginTransaction();
            $this->exec($sql);
            $this->_id = $this->_id ?? $this->lastInsertId();
            $this->commitTransaction();
            $this->_refresh();
            return true;
        } catch (\PDOException $e) {
            $this->rollbackTransaction();
            $this->clear();
            return false;
        }
    }


}