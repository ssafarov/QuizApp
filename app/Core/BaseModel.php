<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/12/19
 * Time: 1:07 PM
 */

namespace Core;


class BaseModel extends AbstractDbAccessor
{
    /**
     * Primary key of the model
     *
     * @var int
     */
    protected $_id;

    /**
     * DB table prefix
     *
     * @var string
     */
    protected $db_table_prefix = 'qzh_';

    /**
     * DB table name of the model
     *
     * @var string
     */
    protected $db_table = 'tablename';

    /**
     * Model relation
     *
     * @var null
     */
    protected $db_belongs_to = null;

    /**
     * Model relation
     *
     * @var null
     */
    protected $db_has_many = null;


    /**
     * Clear model properties except DB related
     *
     * return void
     */
    protected function clear()
    {
        foreach ($this as $key => $value) {
            if (strpos($key, 'db_') === false){
                unset($this->$key);
            }
        }
    }


    /**
     * Check if model exist in DB
     *
     * @param $field string Field name in DB to search
     * @param $value mixed Field value to search
     * @return int|null ID or nullif not found
     */
    protected function checkIfExist($field, $value)
    {
        if (isset($field) && isset($value)) {
            $found = $this->getByField($field, $this->quoteString($value));
            return is_array($found) && !empty($found) && array_key_exists('id', $found[0]) ? intval($found[0]['id']) : null;
        }

        return null;
    }

    /**
     * CHeck if model unique, based on field - value
     *
     * @param $field string Field name in DB to search
     * @param $value mixed Field value to search
     * @return bool True if unique, false otherwise
     */
    protected function checkIfUnique($field, $value)
    {
        if (isset($field) && isset($value)) {
            return count($this->getByField($field, $this->quoteString($value))) > 0 ? true : false;
        }

        return false;
    }

    /**
     * Delete model related records from DB
     *
     * @return bool
     */
    public function delete()
    {
        try {
            $sql = 'DELETE FROM ' . $this->db_table_prefix . $this->db_table . ' WHERE id = ' . $this->_id;
            return $this->exec($sql) !== false;
        } catch (\PDOException $e) {
            return false;
        }
    }


    /**
     * Return total amount of model records in DB
     *
     * @return int
     */
    public function getTotalAmount()
    {
        return count ($this->getAll());
    }

    /**
     * Get all model records from DB
     *
     * @param string $orderBy
     * @return array
     */
    public function getAll($orderBy = '')
    {
        $order = $orderBy ? ' ORDER BY ' . $orderBy : '';
        $stmt = 'SELECT * FROM ' . $this->db_table_prefix . $this->db_table . $order;

        return $this->queryFetchAllAssoc($stmt);
    }

    /**
     * Get the model based on field value
     *
     * @param $field string field name
     * @param null $value value
     * @return array|null
     */
    public function getByField($field, $value = null)
    {
        if (!isset($field)) {
            return null;
        }
        $stmt = 'SELECT * FROM ' . $this->db_table_prefix . $this->db_table . ' WHERE ' . $field . ' = ' . $value;
        return $this->queryFetchAllAssoc($stmt);
    }

    /**
     * Get the model based on ID value
     *
     * @param int|null $id ID field value
     * @return array|null
     */
    public function getById(int $id = null)
    {
        if (empty($id) || !filter_var($id, FILTER_SANITIZE_NUMBER_INT)) {
            return null;
        }

        $stmt = 'SELECT * FROM ' . $this->db_table_prefix . $this->db_table . ' WHERE id = ' . $id;
        return $this->queryFetchRowAssoc($stmt);
    }


    /**
     * Get all related records based on DB relations: BELONGS-TO and HAS-MANY
     *
     * @param $id int ID value of Foreign table
     * @param null $foreign_table Foreign table name
     * @param string $relation Relation direction
     * @return array|null
     */
    public function getAllRelatedAssoc($id, $foreign_table = null, $relation = 'belongs-to')
    {
        $belong = $relation == 'belongs-to'?true:false;

        $table = $belong?$this->db_table:$foreign_table;
        $foreign_key = $belong?$this->db_belongs_to:$this->db_table. '_id';

        if (empty($relation) || empty($id) || empty($foreign_table)) {
            return null;
        }

        $stmt = 'SELECT * FROM ' . $this->db_table_prefix . $table . ' WHERE ' . $foreign_key . ' = ' . $id;

        return $this->queryFetchAllAssoc($stmt);

    }

}