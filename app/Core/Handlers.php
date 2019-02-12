<?php
/**
 * Created by PhpStorm.
 * User: sergei.safarov
 * Date: 2/8/19
 * Time: 1:50 PM
 */

namespace Core;

use Quiz\Config;

/**
 * Error and exception handler class
 *
 */
class Handlers
{
    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     *
     * @return void
     * @throws \ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        // Let's check and keep settings works
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param \Exception $exception The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // set code either 404 or 500 for all errors, simplification
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }

        $logFile = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $logFile);

        $message = "Uncaught exception: '" . get_class($exception) . "' with message '" . $exception->getMessage() . "'.";
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
        $message .= "\nStack trace: " . $exception->getTraceAsString();

        error_log($message);

        http_response_code($code);

        if (!Config::SHOW_ERRORS) {
            View::renderTemplate("$code.html");
        } else {
            echo "<h1>Oops, looks like we have an error here.</h1>";
            echo "<p>Exception Class: <b>'" . get_class($exception) . "'</b></p>";
            echo "<p>Exception Message: <b>'" . $exception->getMessage() . "'</b></p>";
            echo "<p>Thrown in <b>'" . $exception->getFile() . "' on line " . $exception->getLine() . "</b></p>";
            echo "<h4>Stack trace:</h4><p><pre>" . $exception->getTraceAsString() . "</pre></p>";
        }
    }
}