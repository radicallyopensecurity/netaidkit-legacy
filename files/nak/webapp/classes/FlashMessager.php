<?php

class FlashMessager
{
    public function _construct()
    {
        if (!array_key_exists('messages', $_SESSION))
            $_SESSION['messages'] = array();

        if (!array_key_exists('formdata', $_SESSION))
            $_SESSION['formdata'] = array();

        if (!array_key_exists('formerrors', $_SESSION))
            $_SESSION['formerrors'] = array();
    }

    public function addMessage($type, $text, $form)
    {
        $_SESSION['messages'][$form][] = array('type' => $type, 'text' => $text);
    }

    public function addFormData($name, $value, $form)
    {
        $_SESSION['formdata'][$form][$name] = $value;
    }

    public function addFormError($name, $form)
    {
        $_SESSION['formerrors'][$form][$name] = 1;
    }

    public function getMessages($form)
    {
        if ($this->_isAjax())
            return false; // Don't display messages in ajax responses.

        if (empty($_SESSION['messages'][$form]))
            return false;

        $messages = $_SESSION['messages'][$form];
        unset($_SESSION['messages'][$form]);

        return $messages;
    }

    public function getFormData($form, $name)
    {
        if ($this->_isAjax())
            return false; // Don't return form data in ajax responses.

        if (empty($_SESSION['formdata'][$form]) ||
            !array_key_exists($name, $_SESSION['formdata'][$form]))
            return false;

        $value = $_SESSION['formdata'][$form][$name];
        unset($_SESSION['formdata'][$form][$name]);

        return $value;
    }

    public function getFormError($form, $name)
    {
        if ($this->_isAjax())
            return false; // Don't return form errors in ajax responses.

        if (empty($_SESSION['formerrors'][$form]) ||
            !array_key_exists($name, $_SESSION['formerrors'][$form]))
            return false;

        $value = $_SESSION['formerrors'][$form][$name];
        unset($_SESSION['formerrors'][$form][$name]);

        return $value;
    }

    protected function _isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
