<?php

class SettingsController extends Page
{
    protected $_allowed_actions = array('index', 'ap');

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
        $view = new View('settings', $params);
        return $view->display();
    }

    public function ap()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid         = $request->postvar('ssid');
            $key          = $request->postvar('key');
            $key_confirm  = $request->postvar('key_confirm');

            $valid = $this->ap_validate($ssid, $key, $key_confirm);

            if ($valid) {
                $success = NetAidManager::setup_ap($ssid, $key);
                if ($success)
                    $this->_addMessage('info', 'Successfully changed wireless access point.');
            } else {
                // FormData
            }

            if ($request->isAjax()) {
                echo ($valid && $success) ? "SUCCESS" : "FAILURE";
                exit;
            }
        }
    }

    protected function ap_validate($ssid, $key, $key_confirm)
    {
        $valid = true;

        if (!($ssid && $key && $key_confirm)) {
            $valid = false;
            $this->_addMessage('error', 'All fields are required.');
        }

        if (!($key == $key_confirm)) {
            $valid = false;
            $this->_addMessage('error', 'Passwords do not match.');
        }

        $keylen = strlen($key);
        if ($keylen && (($keylen < 8) || ($keylen > 63))) {
            $valid = false;
            $this->_addMessage('error', 'Invalid key length, must be between 8 and 63 characters.');
        }

        return $valid;
    }
}
