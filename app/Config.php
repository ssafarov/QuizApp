<?php

namespace Quiz;

/**
 * Application configuration
 *
 */
class Config
{

    const APP_TITLE = 'Quizz App';

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
    const DB_NAME = 'database-name';

    /**
     * Database user name
     *
     * @var string
     */
    const DB_USER = 'database-user';

    /**
     * Database user password
     *
     * @var string
     */
    const DB_PASSWORD = 'database-password';

    /**
     * Show or hide error messages aka debug mode, but we're always writing logs.
     *
     * @var boolean
     */
    const SHOW_ERRORS = true;
}
