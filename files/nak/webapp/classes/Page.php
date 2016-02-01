<?php

class Page
{
    protected $_allowed_actions = array();
    /**
     * @var Request
     */
    protected $_request;

    public function __construct($request)
    {
        $this->_request = $request;
    }

    public function do_action($action)
    {
        if (!in_array($action, $this->_allowed_actions))
            throw new NotFoundException("Action not defined.");

        if (method_exists($this, $action))
            call_user_func(array($this, $action));
    }

    public function getRequest()
    {
        return $this->_request;
    }

    protected function _redirect($location)
    {
        header('Location: ' . $location);
        die();
    }

    protected function _addMessage($type, $text, $form)
    {
        $flashMessager = new FlashMessager();
        $flashMessager->addMessage($type, $text, $form);
    }

    protected function _addFormData($name, $value, $form)
    {
        $flashMessager = new FlashMessager();
        $flashMessager->addFormData($name, $value, $form);
    }

    protected function _addFormError($name, $form)
    {
        $flashMessager = new FlashMessager();
        $flashMessager->addFormError($name, $form);
    }

    protected function _checkToken($token)
    {
        if (!empty($token) && $token == $_SESSION['token'])
            return true;

        return false;
    }
}
