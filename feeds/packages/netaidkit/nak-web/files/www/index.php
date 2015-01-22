<?php

define('STAGE_DEFAULT', 0);
define('STAGE_OFFLINE', 1);
define('STAGE_ONLINE',  2);
define('STAGE_TOR',     3);
define('STAGE_VPN',     4);

// These defines may never include user controlled values for security reasons.
define('ROOT_DIR'       , realpath(dirname(__FILE__)));
define('VIEW_DIR'       , ROOT_DIR . '/views');
define('CLASS_DIR'      , ROOT_DIR . '/classes');
define('CONTROLLER_DIR' , ROOT_DIR . '/controllers');

include 'classes/Autoloader.php';
spl_autoload_register('Autoloader::load');

$cur_stage = NetAidManager::get_stage();

$request    = new Request($_GET['query']);
$dispatcher = new Dispatcher();

$controller = $request->getController();
$action     = $request->getAction();

if ($cur_stage != STAGE_ONLINE && $_SERVER['SERVER_NAME'] != '192.168.101.1') {
    header('Location: http://192.168.101.1/' . $controller . '/' . $action);
    die();
}

try {
    $page_html = $dispatcher->run($request);
} catch (NotFoundException $e) {
    if ($cur_stage != STAGE_ONLINE) {
        header('Location: http://192.168.101.1/index/index');
        die();
    }
    $e->do_404();
}

$params = array('page_html' => $page_html);
$layout = new View('layout', $params);
$layout->display();
