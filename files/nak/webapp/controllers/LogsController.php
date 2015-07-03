<?php

class LogsController extends Page
{
    protected $_allowed_actions = array('index');

    public function init()
    {
        if ($_SESSION['logged_in'] != 1)
            $this->_redirect('/user/login');
    }

    public function index()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');

        $params = array();
        $view = new View('logs', $params);
        return $view->display();
    }
}
