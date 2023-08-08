<?php
define('ROOT_PATH', __DIR__);

define('ENV', 'development');

define('PROJECT_URL', 'https://notes.fogito.com');


define("TIMEZONE", "Iceland");
define("DEFAULT_TIMEZONE", 100);
define("DEFAULT_USER_TIMEZONE", 101);
define("_MAIN_LANG_", "en");
define("MAX_USERS_LIMIT", 3);

define("MONGO_DB", "notes");
define("APP_ID", 256);
define("SERVER_TOKEN", "n0T3sChXBn2HbmUHdk1AaQDRJasdkAaKs09j2KNl2wka0023l2msa");

define("EMAIL_DOMAIN", "https://mailsend.fogito.com/sendmail.php");
define("EMAIL_KEY", "q1w2e3r4t5aqswdefrgt");

define("DEFAULT_EMAIL", "info@fogito.com");
define("DEFAULT_FROM_NAME", "Fogito TEMPLATE");

define("API_S2S", "https://s2s.fogito.com");
define("CRM_URL", "https://crm.fogito.com");
define("FILE_URL", "https://files.fogito.com");
define('FOGITO_URL', 'https://app.fogito.com');
define("API_FILES", "https://files.fogito.com");
define("API_TIME_TRACKING", "https://timetracking.fogito.com");
define("API_S2S_TASK", 'https://taskmanagement.fogito.com/v2/s2s');




function __fatalErrorHandler()
{
    $error = error_get_last();

    if ($error !== null && in_array($error['type'],
        array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
            E_COMPILE_ERROR, E_COMPILE_WARNING, E_RECOVERABLE_ERROR))) {

        var_dump($error);
        exit();
        Fogito\Http\Response::setJsonContent([
            Fogito\Http\Response::KEY_STATUS  => Fogito\Http\Response::STATUS_ERROR,
            Fogito\Http\Response::KEY_CODE    => Fogito\Http\Response::CODE_ERROR,
            Fogito\Http\Response::KEY_MESSAGE => $error['message'] . ' ' . $error['file'] . ':' . $error['line'],
        ]);
        Fogito\Http\Response::send();
    }
}

register_shutdown_function('__fatalErrorHandler');
