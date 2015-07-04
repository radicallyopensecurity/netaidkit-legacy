<?php

class SettingsController extends Page
{
    protected $_allowed_actions = array('index', 'ap', 'password');

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

    public function password()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $adminpass_check   = $request->postvar('adminpass_check');
            $adminpass         = $request->postvar('adminpass');
            $adminpass_confirm = $request->postvar('adminpass_confirm');

            $valid = $this->password_validate($adminpass_check, $adminpass, $adminpass_confirm);

            if ($valid) {
                $success = NetAidManager::set_adminpass($adminpass);
                if ($success)
                    $this->_addMessage('info', 'Successfully changed administrator password.');
            } else {
                // FormData
            }

            if ($request->isAjax()) {
                echo ($valid && $success) ? "SUCCESS" : "FAILURE";
                exit;
            }
        }
    }

    protected function password_validate($adminpass_check, $adminpass, $adminpass_confirm)
    {
        $valid = true;

        if (!($adminpass_check && $adminpass && $adminpass_confirm)) {
            $valid = false;
            $this->_addMessage('error', 'All fields are required.');
        }

        if (!(NetAidManager::check_adminpass($adminpass_check))) {
            $valid = false;
            $this->_addMessage('error', 'Current admin password is incorrect.');
        }

        if (!($adminpass == $adminpass_confirm)) {
            $valid = false;
            $this->_addMessage('error', 'Passwords do not match.');
        }

        $passlen = strlen($adminpass);
        if ($passlen < 8) {
            $valid = false;
            $this->_addMessage('error', 'Password must be at least 8 characters.');
        }

        return $valid;
    }
}