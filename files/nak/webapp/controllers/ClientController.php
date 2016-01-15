<?php

class ClientController extends Page
{
    protected $_allowed_actions = array('locale', 'set_locale');

    public function locale() {
        if (I18n::settings_get_language() !== false)
            $this->_redirect('/setup/index');

        $params = array();
        $view = new View('setup_lang', $params);
        return $view->display();
    }

    public function set_locale() {
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
