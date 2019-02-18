<?php

namespace Quiz;

/**
 * Application configuration
 *
 */
class Config
{

    /**
     * Application title
     *
     * @var string
     */
    const APP_TITLE = 'Quizz App';

    /**
     * App base URL
     *
     * @var string
     */
    const BASE_URL = 'http://quiz.local/';

    /**
     * Database hostname: localhost, 127.0.0.1 or whatever
     *
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     *
     * @var string
     */
    const DB_NAME = 'quizapp_local';

    /**
     * Database user name
     *
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database user password
     *
     * @var string
     */
    const DB_PASSWORD = 'root';

    /**
     * Show or hide error messages aka debug mode, but we're always writing logs.
     *
     * @var boolean
     */
    const SHOW_ERRORS = true;
}
