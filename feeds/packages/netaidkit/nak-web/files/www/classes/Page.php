<?php

class Page
{
    protected $_allowed_actions = array();
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
    
    protected function _addMessage($type, $text)
    {
        $flashMessager = new FlashMessager();
        $flashMessager->addMessage($type, $text);
    }
}
