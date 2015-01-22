<?php

class Request
{
    protected $_params   = array();
    protected $_postvars = array();
    
    protected $_controller;
    protected $_action;

    public function __construct($query)
    {
        if (!empty($query))
            $this->_params = preg_split('/\//', $query, 
                                        NULL, PREG_SPLIT_NO_EMPTY);
        
        if (!empty($_POST))
            $this->_postvars = $_POST;
        
        $n_params = sizeof($this->_params);

        $this->_controller = ($n_params > 0) ? $this->_params[0] : 'index';
        $this->_action     = ($n_params > 1) ? $this->_params[1] : 'index';
    }
    
    public function postvar($key)
    {
        if (!array_key_exists($key, $this->_postvars))
            return false;
            
        return $this->_postvars[$key];
    }
    
    public function isPost()
    {
        return !empty($this->_postvars);
    }
    
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    public function getController()
    {
        return $this->_controller;
    }
    
    public function getAction()
    {
        return $this->_action;
    }
}
