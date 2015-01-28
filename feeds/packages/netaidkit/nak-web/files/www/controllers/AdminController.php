<?php

class AdminController extends Page
{
    protected $_torLogfile = '/var/log/tor/notices.log';

    protected $_allowed_actions = array('index', 'toggle_tor', 'tor_status', 'get_wifi', 'wan', 'toggle_vpn');
    
    public function index()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');
    
        $wan_ssid = NetAidManager::wan_ssid();
        $cur_stage = NetAidManager::get_stage();
        
        
        $params = array('cur_stage' => $cur_stage, 'wan_ssid' => $wan_ssid);
        $view = new View('admin', $params);
        return $view->display();
    }
    
    public function toggle_tor()
    {
        $request = $this->getRequest();
        $tor_success = NetAidManager::toggle_tor();
        
        if ($tor_success) {
            if ($request->isAjax()) {
                echo $tor_success ? "SUCCESS" : "FAILURE";
                exit;
            } else {
                $this->_redirect('admin/index');
            }
        }
    }
    
    public function tor_status()
    {
        if (file_exists($this->_torLogfile)) {
            die('<pre>' . file_get_contents($this->_torLogfile) . '</pre>');
        } else {
            die('not running');
        }
    }
    
    public function toggle_vpn()
    {
        $request = $this->getRequest();
        $vpn_success = NetAidManager::toggle_vpn();
        
        if ($vpn_success) {
            if ($request->isAjax()) {
                echo $vpn_success ? "SUCCESS" : "FAILURE";
                exit;
            } else {
                $this->_redirect('admin/index');
            }
        }
    }
    
    public function get_wifi()
    {
        $request = $this->getRequest();
        
        if ($request->isAjax()) {
            $wifi_list = NetAidManager::scan_wifi();
            
            $params = array('wifi_list' => $wifi_list);
            $view = new View('wifi_ajax', $params);
            $view->display();
            exit;
        }
    }
    
    public function wan()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid = $request->postvar('ssid');
            $key  = $request->postvar('key');
            
            $wan_success  = NetAidManager::setup_wan($ssid, $key);

            if ($request->isAjax()) {
                echo $wan_success ? "SUCCESS" : "FAILURE";
                exit;
            }
        }
    }
}
