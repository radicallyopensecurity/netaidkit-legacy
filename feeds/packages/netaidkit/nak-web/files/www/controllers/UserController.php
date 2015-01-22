<?php

class UserController extends Page
{
    protected $_allowed_actions = array('index', 'login', 'logout');
    
    public function index()
    {
        $this->_redirect('/user/login');
    }
    
    public function login()
    {
        $view = new View('login');
        return $view->display();
    }
    
    public function logout()
    {
    
    }
}
