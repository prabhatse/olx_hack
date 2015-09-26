<?php
/************************************************/
/***  APPLICATION CONFIG                      ***/
/************************************************/

$EFC_CFG['app_title_prefix']                 = "";


// Public path of EFC's source folder on your web server.
// For example, if EFC is located 
$EFC_CFG['site_root_path']            = '/EFC/webapp/';

// Server path to source code folder, dirname( __FILE__ ) . '/'; by default
$EFC_CFG['source_root_path']          = dirname( __FILE__ ) . '/';

// Server path to writable data directory, $EFC_CFG['source_root_path'] . 'data/' by default
$EFC_CFG['datadir_path']              = $EFC_CFG['source_root_path'] . 'data/';

// File Upload paths
$EFC_CFG['csv_upload']                  = 'upload/csv/';
$EFC_CFG['pdf_upload']                  = 'upload/pdf/';
$EFC_CFG['word_upload']                  = 'upload/word/';
$EFC_CFG['text_upload']                  = 'upload/text/';
$EFC_CFG['img_upload']                  = 'upload/img/';

$EFC_CFG['gmapJson']                  = 'https://maps.googleapis.com/maps/api/geocode/json?address=';

// Your timezone
$EFC_CFG['timezone']                  = 'Indian/Antananarivo';

// Toggle Smarty caching. true: Smarty caching on, false: Smarty caching off
$EFC_CFG['cache_pages']               = true;
//
// Should sessions be stored in the database?  (As opposed to the php default files)
$EFC_CFG['use_db_sessions']               = true;

// Smarty file cache lifetime in seconds; defaults to 600 (10 minutes) caching
$EFC_CFG['cache_lifetime']               = 600;

// The crawler, when triggered by requests to the RSS feed, will only launch if it's been
// 20 minutes or more since the last crawl.
$EFC_CFG['rss_crawler_refresh_rate']  = 20;

// Optional Mandrill API key. Set this to a valid key to send email via Mandrill instead of PHP's mail() function..
// Get key at https://mandrillapp.com/settings/ in "SMTP & API Credentials"
$EFC_CFG['mandrill_api_key'] = 'jZ-83lBbJI9JlrA-w3em2Q';
$EFC_CFG['fb_app_id'] = '1597632873793798';
$EFC_CFG['fb_app_secret'] = '0265344582d7b5aac063b670c991699b';


/************************************************/
/***  DATABASE CONFIG                         ***/
/************************************************/

$EFC_CFG['db_host']                   = 'localhost'; //On a shared host? Try mysql.yourdomain.com, or see your web host's documentation.
$EFC_CFG['db_type']                   = 'mysql';
$EFC_CFG['db_user']                   = 'root';
$EFC_CFG['db_password']               = 'abc123';
$EFC_CFG['db_name']                   = 'stern';
$EFC_CFG['db_socket']                 = '';
$EFC_CFG['db_port']                   = '';
$EFC_CFG['table_prefix']              = 'si_';

/************************************************/
/***  DEVELOPER CONFIG                        ***/
/************************************************/

// Full server path to crawler.log.
// $EFC_CFG['log_location']              = $EFC_CFG['datadir_path'] . 'logs/crawler.log';
$EFC_CFG['log_location']              = $EFC_CFG['datadir_path'].'logs';

// Verbosity of log. 0 is everything, 1 is user messages, 2 is errors only
$EFC_CFG['log_verbosity']             = 0;

//Logger_profiler enabler to generate profiler
$EFC_CFG['logger_profiler']             = false;

// Full server path to stream processor log.
// $EFC_CFG['stream_log_location']       = $EFC_CFG['datadir_path'] . 'logs/stream.log';
$EFC_CFG['stream_log_location']       = true;

// Full server path to sql.log. To not log queries, set to null.
$EFC_CFG['sql_log_location']          = $EFC_CFG['datadir_path'] . 'logs/sql.log';
//$EFC_CFG['sql_log_location']          = null;

// How many seconds does a query take before it gets logged as a slow query?
$EFC_CFG['slow_query_log_threshold']  = 2.0;

$EFC_CFG['debug']                     = false;

$EFC_CFG['enable_profiler']           = false;

// Set this to true if you want your PDO object's database connection's charset to be explicitly set to utf8.
// If false (or unset), the database connection's charset will not be explicitly set.
$EFC_CFG['set_pdo_charset']           = false;

//TESTS OVERRIDE: Assign variables below to use different settings during test builds
if ((isset($_COOKIE['TU_MODE']) && $_COOKIE['TU_MODE']=='TESTS') && ! isset($_SESSION["RD_MODE"])
|| (getenv("MODE")=="TESTS" && ! getenv("RD_MODE")=="1")) {
    //    $EFC_CFG['source_root_path']          = dirname( __FILE__ ) . '/';
    //    $EFC_CFG['db_user']                   = 'root';
    //    $EFC_CFG['db_password']               = '';
    //    $EFC_CFG['db_name']                   = 'EFC'; //by default, stern_tests
    $EFC_CFG['cache_pages']               = false;
    $EFC_CFG['debug']                     = true;
    $EFC_CFG['timezone']                  = 'Indian/Antananarivo';
    ini_set('error_reporting', E_STRICT);
}

//Test RAM disk database override: Set this to run tests against the RAM disk tests database
if (isset($_SESSION["RD_MODE"]) || getenv("RD_MODE")=="1") {
    //    $EFC_CFG['db_user']                   = 'root';
    //    $EFC_CFG['db_password']               = '';
    //    $EFC_CFG['db_name']                   = 'EFC';
}

//Set aggressive time limit for long crawls
set_time_limit(500);
