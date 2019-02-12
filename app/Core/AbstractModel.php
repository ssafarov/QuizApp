<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/8/19
 * Time: 2:09 PM
 */

namespace Core;


use PDO;
use Quiz\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class AbstractModel
{

    /**
     * The singleton instance
     *
     */
    static private $PDOInstance;


    /**
     * Creates/Get a PDO singleton instance
     *
     * @return PDO
     */
    public function __construct()
    {
        $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';

        if(!self::$PDOInstance) {

            self::$PDOInstance = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            // Throw an Exception when an error occurs
            self::$PDOInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        return self::$PDOInstance;
    }

    /**
     * Initiates a new DB transaction
     *
     * @return bool
     */
    public function beginTransaction() {
        return self::$PDOInstance->beginTransaction();
    }

    /**
     * Commits an existing DB transaction
     *
     * @return bool
     */
    public function commitTransaction() {
        return self::$PDOInstance->commit();
    }

    /**
     * Rollback an existing DB transaction
     *
     * @return bool
     */
    public function rollbackTransaction() {
        return self::$PDOInstance->rollBack();
    }


    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle
     *
     * @return string
     */
    public function errorCode() {
        return self::$PDOInstance->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array
     */
    public function errorInfo() {
        return self::$PDOInstance->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     * @return int|bool
     */
    public function exec($statement) {
        return self::$PDOInstance->exec($statement);
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name) {
        return self::$PDOInstance->lastInsertId($name);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement object returned
     * @return \PDOStatement|bool
     */
    public function prepare ($statement, $driver_options=[]) {
        return self::$PDOInstance->prepare($statement, $driver_options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return \PDOStatement
     */
    public function query($statement) {
        return self::$PDOInstance->query($statement);
    }

    /**
     * Execute query and return all rows in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchAllAssoc($statement) {
        return self::$PDOInstance->query($statement)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and return one row in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchRowAssoc($statement) {
        return self::$PDOInstance->query($statement)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and select one column only
     *
     * @param string $statement
     * @return mixed
     */
    public function queryFetchColAssoc($statement) {
        return self::$PDOInstance->query($statement)->fetchColumn();
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quoteString ($input, $parameter_type=0) {
        return self::$PDOInstance->quote($input, $parameter_type);
    }

}