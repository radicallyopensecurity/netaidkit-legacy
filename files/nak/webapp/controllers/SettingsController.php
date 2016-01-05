<?php

class SettingsController extends Page
{
    protected $_allowed_actions = array('index', 'ap', 'password', 'localization');

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
            $token        = $request->postvar('token');

            if (!$this->_checkToken($token))
                exit(-1);

            $valid = $this->ap_validate($ssid, $key, $key_confirm);

            if ($valid) {
                $success = NetAidManager::setup_ap($ssid, $key);
                if ($success)
                    $this->_addMessage('info', _('Successfully changed wireless access point.'), 'ap');
            } else {
                $this->_addFormData('ssid', $ssid, 'ap');
                $this->_addFormData('key', $key, 'ap');
                $this->_addFormData('key_confirm', $key_confirm, 'ap');
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
            $this->_addMessage('error', _('All fields are required.'), 'ap');

            if (empty($ssid))
                $this->_addFormError('ssid', 'ap');

            if (empty($key))
                $this->_addFormError('key', 'ap');

            if (empty($key_confirm))
                $this->_addFormError('key_confirm', 'ap');
        }

        if (!($key == $key_confirm)) {
            $valid = false;
            /// TRANSLATORS: in key confirmation context
            $this->_addMessage('error', _('Keys do not match.'), 'ap');
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
                    $this->_addMessage('info', _('Successfully changed administrator password.'), 'pwd');
            } else {
                $this->_addFormData('adminpass_check', $adminpass_check, 'pwd');
                $this->_addFormData('adminpass', $adminpass, 'pwd');
                $this->_addFormData('adminpass_confirm', $adminpass_confirm, 'pwd');
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
            $this->_addMessage('error', _('All fields are required.'), 'pwd');

            if (empty($adminpass_check))
                $this->_addFormError('adminpass_check', 'pwd');

            if (empty($adminpass))
                $this->_addFormError('adminpass', 'pwd');

            if (empty($adminpass_confirm))
                $this->_addFormError('adminpass_confirm', 'pwd');
        }

        if (!(NetAidManager::check_adminpass($adminpass_check))) {
            $valid = false;
            $this->_addMessage('error', _('Current admin password is incorrect.'), 'pwd');
            $this->_addFormError('adminpass_check', 'pwd');
        }

        if (!($adminpass == $adminpass_confirm)) {
            $valid = false;
            $this->_addMessage('error', _('Passwords do not match.'), 'pwd');
            $this->_addFormError('adminpass', 'pwd');
            $this->_addFormError('adminpass_confirm', 'pwd');
        }

        $passlen = strlen($adminpass);
        if ($passlen < 8) {
            $valid = false;
            $this->_addMessage('error', _('Password must be at least 8 characters.'), 'pwd');
            $this->_addFormError('adminpass', 'pwd');
            $this->_addFormError('adminpass_confirm', 'pwd');
        }

        return $valid;
    }

    public function localization()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $lang = $request->postvar('language');
            if (empty($lang))
                return;

            if ($lang == 'auto') { 
                I18n::settings_set_autodetect();
            } else {
                // sanitized in I18n class
                I18n::settings_set_language($lang);
            }
        }
    }
}
