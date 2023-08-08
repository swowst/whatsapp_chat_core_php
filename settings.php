<?php
header("Access-Control-Allow-Origin: *");

const _MODULES = [
    "default",
    "users",
    "usersall",
    "permissions",
];

define("APP_ID", 256);
define("SERVER_TOKEN", "n0T3sChXBn2HbmUHdk1AaQDRJasdkAaKs09j2KNl2wka0023l2msa");

define("MONGO_DB", "notes");
define('PROJECT_URL', 'https://notes.fogito.com');

define('DB_HOST', 'localhost');
define('DB_PORT', 27017);
define('DB_USERNAME', false);
define('DB_PASSWORD', false);

define("TIMEZONE", "Iceland");
ini_set('date.timezone', TIMEZONE);

define("DEFAULT_TIMEZONE", 100);
define("DEFAULT_USER_TIMEZONE", 101);
define("_MAIN_LANG_", "en");
define("_LANG_", "en");
define("MAX_USERS_LIMIT", 3);


define("API_FILES", "https://files.fogito.com");
define("CRM_URL", "https://crm.fogito.com");
define("FILE_URL", "https://files.fogito.com");
define("API_S2S", "https://s2s.fogito.com");
