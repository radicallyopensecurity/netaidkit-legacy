<?php

require_once('/nak/webapp/bootstrap.php');

if (!session_id())
    session_start();

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
