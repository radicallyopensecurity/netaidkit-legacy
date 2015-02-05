<?php

class AdminController extends Page
{
    protected $_torLogfile = '/var/log/tor/notices.log';

    protected $_allowed_actions = array('index', 'toggle_tor', 'tor_status', 
                                        'get_wifi', 'wan', 'toggle_vpn', 
                                        'upload_vpn', 'delete_vpn');
    
    public function index()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_OFFLINE)
            $this->_redirect('/setup/wan');
    
        $wan_ssid = NetAidManager::wan_ssid();
        $cur_stage = NetAidManager::get_stage();
        
        $vpn_obj = new Ovpn();
        $vpn_options = $vpn_obj->getOptions();

        $params = array('cur_stage' => $cur_stage, 'wan_ssid' => $wan_ssid, 'vpn_options' => $vpn_options);
        $view = new View('admin', $params);
        return $view->display();
    }
    

    public function upload_vpn() 
    {
        $vpn_obj = new Ovpn();
        if ($vpn_obj->handleUpload())
            $this->_addMessage('info', 'VPN config file uploaded.');
        else
            $this->_addMessage('error', 'File upload failed.');

        $this->_redirect('/admin/index');
    }
    
    public function delete_vpn()
    {
        $request = $this->getRequest();
        $file = $request->postvar('file');
    
        $vpn_obj = new Ovpn();
        if ($vpn_obj->removeFile($file))
            $this->_addMessage('info', 'VPN config file removed.');
        else
            $this->_addMessage('error', 'Could not remove file.');

        $this->_redirect('/admin/index');
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
            $log = file_get_contents($this->_torLogfile);
            
            preg_match_all('/Bootstrapped (\d{1,3})\%/', $log, $bootstrap);
            
            $status = 'Starting';
            
            if (!empty($bootstrap[1])) {
                $progress = end(array_values($bootstrap[1]));
                if ($progress == '100')
                    $status = 'Running.';
                else 
                    $status = "Bootstrapping: $progress%";
            }
            
            die($progress);
        } else {
            die('not running');
        }
    }
    
    public function toggle_vpn()
    {
        $request = $this->getRequest();
        $ovpn_obj = new Ovpn();
        $ovpn_file = $ovpn_obj->ovpn_root . '/upload/' . $request->postvar('file');
        
        if (file_exists($ovpn_file)) {
            $ovpn_file = escapeshellarg($ovpn_file);
            $current = escapeshellarg($ovpn_obj->ovpn_root . '/current.ovpn');
            shell_exec("ln -s $ovpn_file $current");
        }
        
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
