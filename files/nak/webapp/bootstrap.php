<?php

// These defines may never include user controlled values for security reasons.
define('ROOT_DIR'       , realpath(dirname(__FILE__)));
define('VIEW_DIR'       , ROOT_DIR . '/views');
define('CLASS_DIR'      , ROOT_DIR . '/classes');
define('CONTROLLER_DIR' , ROOT_DIR . '/controllers');

// NetAidKit stages.
define('STAGE_DEFAULT', 0);
define('STAGE_OFFLINE', 1);
define('STAGE_ONLINE',  2);
define('STAGE_TOR',     3);
define('STAGE_VPN',     4);

// Register class autoloader.
include 'classes/Autoloader.php';
spl_autoload_register('Autoloader::load');
