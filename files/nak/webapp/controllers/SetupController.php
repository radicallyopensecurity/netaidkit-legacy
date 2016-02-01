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

        /* All stages higher then STAGE_ONLINE are versions "ONLINE" versions,
         * so redirect to admin/index */
        if ($cur_stage >= STAGE_ONLINE)
            $this->_redirect('/admin/index');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid              = $request->postvar('ssid');
            $key               = $request->postvar('key');
            $key_confirm       = $request->postvar('key_confirm');
            $adminpass         = $request->postvar('adminpass');
            $adminpass_confirm = $request->postvar('adminpass_confirm');
            $distresspass      = $request->postvar('distresspass');

            $valid = $this->ap_validate($ssid, $key, $adminpass, $key_confirm, $adminpass_confirm);

            $success = false;
            if ($valid) {
                $ap_success = NetAidManager::setup_ap($ssid, $key);
                $pass_success = NetAidManager::set_adminpass($adminpass);
                $success = ($ap_success && $pass_success);

                if ($success) {
                    NetAidManager::set_stage(STAGE_OFFLINE);
                    $this->_addMessage('info', _('Access Point successfully set up.'), 'wan');
                }
            } else {
                $this->_addFormData('ssid', $ssid, 'ap');
                $this->_addFormData('key', $key, 'ap');
                $this->_addFormData('key_confirm', $key_confirm, 'ap');
                $this->_addFormData('adminpass', $adminpass, 'ap');
                $this->_addFormData('adminpass_confirm', $adminpass_confirm, 'ap');
            }

            if ($request->isAjax()) {
                echo ($valid && $success) ? "SUCCESS" : "FAILURE";
                exit;
            }
        }

        $view = new View('setup_ap');
        return $view->display();
    }

    protected function ap_validate($ssid, $key, $adminpass, $key_confirm, $adminpass_confirm)
    {
        $valid = true;

        if (!($ssid && $key && $adminpass && $key_confirm && $adminpass_confirm)) {
            $valid = false;
            $this->_addMessage('error', _('All fields are required.'), 'ap');

            if (empty($ssid))
                $this->_addFormError('ssid', 'ap');

            if (empty($key))
                $this->_addFormError('key', 'ap');

            if (empty($adminpass))
                $this->_addFormError('adminpass', 'ap');

            if (empty($key_confirm))
                $this->_addFormError('key_confirm', 'ap');

            if (empty($adminpass_confirm))
                $this->_addFormError('adminpass_confirm', 'ap');
        }

        if ($key != $key_confirm) {
            $valid = false;
            $this->_addMessage('error', _('Wireless key does not match.'), 'ap');
            $this->_addFormError('key', 'ap');
            $this->_addFormError('key_confirm', 'ap');
        }

        $keylen = strlen($key);
        if ($keylen && (($keylen < 8) || ($keylen > 63))) {
            $valid = false;
            $this->_addMessage('error', _('Invalid key length, must be between 8 and 63 characters.'), 'ap');
            $this->_addFormError('key', 'ap');
            $this->_addFormError('key_confirm', 'ap');
        }

        if ($adminpass != $adminpass_confirm) {
            $valid = false;
            $this->_addMessage('error', _('Admin password does not match.'), 'ap');
            $this->_addFormError('adminpass', 'ap');
            $this->_addFormError('adminpass_confirm', 'ap');
        }

        $passlen = strlen($adminpass);
        if ($passlen < 8) {
            $valid = false;
            $this->_addMessage('error', _('Admin password must be at least 8 characters.'), 'ap');
            $this->_addFormError('adminpass', 'ap');
            $this->_addFormError('adminpass_confirm', 'ap');
        }

        return $valid;
    }

    public function wan()
    {
        $cur_stage = NetAidManager::get_stage();
        if ($cur_stage == STAGE_DEFAULT)
            $this->_redirect('/setup/ap');
        if ($cur_stage == STAGE_ONLINE)
            $this->_redirect('/admin/index');

        if (NetAidManager::get_inetstat()) {
                NetAidManager::go_online();
                NetAidManager::set_stage(STAGE_ONLINE);
                $this->_addMessage('info', _('Setup complete.'), 'setup');
                $this->_redirect('/admin/index');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ssid = $request->postvar('ssid');
            $key  = $request->postvar('key');

            $wan_success  = NetAidManager::setup_wan($ssid, $key);

            if ($wan_success) {
                NetAidManager::set_stage(STAGE_ONLINE);
                $this->_addMessage('info', _('Setup complete.'), 'setup');
            }

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
