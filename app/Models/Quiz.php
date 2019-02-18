<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 12:27 PM
 */

namespace Quiz\Models;


use Core\BaseModel;

class Quiz extends BaseModel
{


    /**
     * Quiz title property
     *
     * @var string
     */
    private $_title;

    /**
     * Quiz short description
     *
     * @var string
     */
    private $_description;

    /**
     * Related questions
     *
     * @var array
     */
    private $_questions;

    /**
     * DB relation table and direction
     *
     * @var string
     */
    protected $db_table = 'quiz';
    protected $db_belongs_to = '';
    protected $db_has_many = 'question';


    /**
     * Quiz constructor.
     *
     * @param int|null $id
     */
    public function __construct(int $id = null)
    {
        parent::__construct();

        $this->_id = $id;

        $this->refreshModel();

    }

    /**
     * Refresh model from the DB data
     *
     */
    private function refreshModel()
    {
        $fields = $this->getById($this->_id);

        if (is_array($fields) && !empty($fields)) {
            $this->_title = array_key_exists('title', $fields) ? $fields['title'] : '';
            $this->_description = (array_key_exists('description', $fields)&&!empty($fields['description'])) ? $fields['description'] : '';
            $this->getQuestions();
        }
    }
    /**
     * Get related questions models from DB
     *
     * @return $this
     */
    private function getQuestions()
    {
        $this->_questions = [];
        $arrQuestions = $this->getAllRelatedAssoc($this->_id, $this->db_has_many, 'has-many');

        if (!empty($arrQuestions)) {
            foreach ($arrQuestions as $arrQuestion) {
                array_push($this->_questions, new Question($this, $arrQuestion['id']));
            }
        }
    }

    /**
     * Get model from DB
     *
     * @return $this
     */
    public function get()
    {
        $this->refreshModel();
        return $this;
    }

    /**
     * Get/Set model ID
     *
     * @param string|null $value
     *
     * @return mixed
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
    public function title(string $value = null)
    {
        if (empty($value))
            return $this->_title;
        else
            $this->_title = filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Get/Set model description
     *
     * @param string|null $value
     *
     * @return mixed
     */
    public function description(string $value = null)
    {
        if (empty($value))
            return $this->_description;
        else
            $this->_description = filter_var($value, FILTER_SANITIZE_STRING);;
    }

    /**
     * Return questions related
     *
     * @return Quiz
     */
    public function questions()
    {
        return $this->_questions;
    }

    /**
     * Save model data into DB
     *
     * @return bool
     */
    public function save()
    {
        if (empty($this->_title))
            return false;

        $this->_id = $this->_id??$this->checkIfExist('title', $this->_title);

        if ($this->_id) {
            $sql = 'UPDATE ' . $this->db_table_prefix . $this->db_table . ' SET title = ' . $this->quoteString($this->_title) . ', description = ' . $this->quoteString($this->_description) . ' WHERE id = ' . $this->_id;
        } else {
            $sql = 'INSERT INTO ' . $this->db_table_prefix . $this->db_table . ' (title, description) VALUES (' . $this->quoteString($this->_title) . ', ' . $this->quoteString($this->_description) . ')';
        }

        try {
            $this->beginTransaction();
            $this->exec($sql);
            $this->_id = $this->_id ?? $this->lastInsertId();
            $this->commitTransaction();
            return true;
        } catch (\PDOException $e) {
            $this->rollbackTransaction();
            return false;
        }

    }

}