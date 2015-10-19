<?php

class NetworkController extends Page
{
    protected $_allowed_actions = array('index', 'save');

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

        $broadcast_hidden_status = NetAidManager::broadcast_hidden_status();

        $params = array('broadcast_hidden_status' => $broadcast_hidden_status);
        $view = new View('network', $params);
        return $view->display();
    }

    public function save()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $broadcast_ssid = $request->postvar('broadcast_ssid');

            $mode = $broadcast_ssid ? 'on' : 'off';
            $success = NetAidManager::toggle_broadcast($mode);

            if ($success)
                $this->_addMessage('info', 'Successfully saved network settings.', 'network');

            if ($request->isAjax()) {
                echo ($success) ? "SUCCESS" : "FAILURE";
                exit;
            }
        };
    }
}
