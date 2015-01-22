<?php

class SetupController extends Page
{
    protected $_allowed_actions = array('index', 'ap', 'wan', 'disconnected');
    
    public function index()
    {
        $this->_redirect('/setup/ap');
    }
    
    public function ap()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');
        if ($cur_stage == STAGE_ONLINE)
            $this->_redirect('/admin/index');
            
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid         = $request->postvar('ssid');
            $key          = $request->postvar('key');
            $adminpass    = $request->postvar('adminpass');
            $distresspass = $request->postvar('distresspass');
            
            $ap_success = NetAidManager::setup_ap($ssid, $key);
            $pass_success = NetAidManager::set_adminpass($adminpass);
            $success = ($ap_success);

            if ($success)
                NetAidManager::set_stage(STAGE_OFFLINE);

            if ($request->isAjax()) {
                echo $ap_success ? "SUCCESS" : "FAILURE";
                exit;
            }
        }
        
        $view = new View('setup_ap');
        return $view->display();
    }
    
    public function wan()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_ONLINE)
            $this->_redirect('/admin/index');
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid = $request->postvar('ssid');
            $key  = $request->postvar('key');
            
            $wan_success  = NetAidManager::setup_wan($ssid, $key);
            
            if ($wan_success)
                NetAidManager::set_stage(STAGE_ONLINE);

            if ($request->isAjax()) {
                echo $wan_success ? "SUCCESS" : "FAILURE";
                exit;
            }
        }
    
        $wifi_list = NetAidManager::scan_wifi();
        
        $params = array('wifi_list' => $wifi_list);
        $view = new View('setup_wan', $params);
        return $view->display();
    }
    
    public function disconnected()
    {
        echo 'disconnected';
    }
}
